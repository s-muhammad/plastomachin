const SVG_NS = 'http://www.w3.org/2000/svg';
const faFormat = new Intl.NumberFormat('fa-IR');

const STATUS_STYLES = {
    planned: { fill: '#94a3b8', stroke: '#64748b', badge: 'border-slate-200 bg-slate-100 text-slate-600' },
    writing: { fill: '#f6a21a', stroke: '#b45309', badge: 'border-amber-200 bg-amber-50 text-amber-700' },
    published: { fill: '#10b981', stroke: '#047857', badge: 'border-emerald-200 bg-emerald-50 text-emerald-700' },
    needs_review: { fill: '#f43f5e', stroke: '#be123c', badge: 'border-rose-200 bg-rose-50 text-rose-700' },
};

const TASK_STATUS_CLASSES = {
    todo: 'border-slate-200 bg-slate-100 text-slate-600',
    doing: 'border-amber-200 bg-amber-50 text-amber-700',
    done: 'border-emerald-200 bg-emerald-50 text-emerald-700',
};

const GROUP_MODES = ['page', 'page_type', 'role'];

const fa = (value) => {
    if (value === null || value === undefined || value === '') return '—';
    const number = Number(value);
    return Number.isFinite(number) ? faFormat.format(number) : String(value);
};

const svgElement = (name, attributes = {}) => {
    const element = document.createElementNS(SVG_NS, name);

    Object.entries(attributes).forEach(([key, value]) => {
        if (value !== null && value !== undefined) {
            element.setAttribute(key, String(value));
        }
    });

    return element;
};

const shorten = (text, max = 26) => {
    const value = String(text ?? '');
    return value.length > max ? `${value.slice(0, max - 1)}…` : value;
};

const splitPhrases = (text) => {
    if (!text) return [];
    return String(text)
        .split(/[،,;؛\n\r]+/)
        .map((part) => part.trim())
        .filter(Boolean);
};

const clamp = (value, min, max) => Math.min(max, Math.max(min, value));

const createRandom = (seed) => {
    let state = seed >>> 0;

    return () => {
        state = (state + 0x6d2b79f5) >>> 0;
        let t = state;
        t = Math.imul(t ^ (t >>> 15), t | 1);
        t ^= t + Math.imul(t ^ (t >>> 7), t | 61);
        return ((t ^ (t >>> 14)) >>> 0) / 4294967296;
    };
};

export function initSeoGraph() {
    const root = document.querySelector('[data-seo-graph]');
    const dataElement = document.getElementById('seo-graph-data');
    const svg = root?.querySelector('[data-graph-svg]');

    if (!root || !dataElement || !svg) return;

    let data;

    try {
        data = JSON.parse(dataElement.textContent || '{}');
    } catch (error) {
        console.error('SEO graph data could not be parsed:', error);
        return;
    }

    if (!Array.isArray(data.nodes) || data.nodes.length === 0) return;

    const countElement = root.querySelector('[data-graph-count]');
    const groupSelect = root.querySelector('[data-graph-group]');
    const relatedToggle = root.querySelector('[data-graph-related]');
    const labelsToggle = root.querySelector('[data-graph-labels]');
    const resetButton = root.querySelector('[data-graph-reset]');
    const zoomInButton = root.querySelector('[data-graph-zoom-in]');
    const zoomOutButton = root.querySelector('[data-graph-zoom-out]');
    const fitButton = root.querySelector('[data-graph-fit]');

    const panel = document.querySelector('[data-brief]');
    const emptyPanel = panel?.querySelector('[data-brief-empty]');
    const contentPanel = panel?.querySelector('[data-brief-content]');
    const groupPanel = panel?.querySelector('[data-brief-group]');
    const chipTemplate = document.querySelector('[data-brief-chip-template]');
    const taskTemplate = document.querySelector('[data-brief-task-template]');
    const keywordChipTemplate = document.querySelector('[data-brief-keyword-chip-template]');

    const viewport = svgElement('g');
    const linkLayer = svgElement('g');
    const nodeLayer = svgElement('g');
    viewport.append(linkLayer, nodeLayer);
    svg.append(viewport);

    const state = {
        mode: GROUP_MODES.includes(groupSelect?.value) ? groupSelect.value : 'page',
        showRelated: relatedToggle ? relatedToggle.checked : true,
        showLabels: labelsToggle ? labelsToggle.checked : false,
        nodes: [],
        links: [],
        groups: new Map(),
        nodeById: new Map(),
        selectedId: null,
        hoveredId: null,
        transform: { x: 0, y: 0, k: 1 },
        drag: null,
        pan: null,
        suppressClick: false,
    };

    const groupKey = (keyword, mode) => {
        if (mode === 'page_type') return keyword.groups.page_type || 'other';
        if (mode === 'role') return keyword.groups.role || 'other';
        return keyword.groups.page || '__none__';
    };

    const groupLabel = (keyword, mode) => {
        if (mode === 'page_type') {
            return data.labels?.page_types?.[keyword.groups.page_type] || keyword.pageTypeLabel || '—';
        }

        if (mode === 'role') {
            return data.labels?.roles?.[keyword.groups.role] || keyword.roleLabel || '—';
        }

        return keyword.groups.page || 'بدون صفحه هدف';
    };

    const buildModel = () => {
        const random = createRandom(1979);
        const keywordNodes = data.nodes.map((keyword) => ({
            id: `k${keyword.id}`,
            keywordId: keyword.id,
            type: 'keyword',
            label: keyword.keyword,
            keyword,
            r: 7 + (3 - keyword.priority) * 2 + Math.min(3, keyword.tasks?.length || 0) + (keyword.openTasksCount > 0 ? 1 : 0),
            x: 0,
            y: 0,
            vx: 0,
            vy: 0,
        }));

        const rootNode = {
            id: 'root',
            type: 'root',
            label: 'برنامه سئو',
            r: 30,
            x: 0,
            y: 0,
            vx: 0,
            vy: 0,
        };

        const groups = new Map();

        keywordNodes.forEach((node) => {
            const key = groupKey(node.keyword, state.mode);

            if (!groups.has(key)) {
                groups.set(key, {
                    id: `g:${key}`,
                    type: 'hub',
                    key,
                    label: groupLabel(node.keyword, state.mode),
                    items: [],
                    x: 0,
                    y: 0,
                    vx: 0,
                    vy: 0,
                });
            }

            groups.get(key).items.push(node);
        });

        const nodes = [rootNode, ...groups.values(), ...keywordNodes];
        const links = [];
        const hubList = [...groups.values()];

        hubList.forEach((hub, hubIndex) => {
            hub.r = 13 + Math.min(11, hub.items.length * 1.3);

            const angle = (hubIndex / Math.max(1, hubList.length)) * Math.PI * 2 - Math.PI / 2;
            const distance = 205 + random() * 20;
            hub.x = Math.cos(angle) * distance;
            hub.y = Math.sin(angle) * distance;

            links.push({ source: rootNode.id, target: hub.id, kind: 'bridge', length: 200, strength: 0.014 });

            hub.items.forEach((node, itemIndex) => {
                const itemAngle = (itemIndex / Math.max(1, hub.items.length)) * Math.PI * 2 + random() * 0.4;
                const itemDistance = 54 + hub.items.length * 6 + random() * 12;
                node.x = hub.x + Math.cos(itemAngle) * itemDistance;
                node.y = hub.y + Math.sin(itemAngle) * itemDistance;

                links.push({ source: hub.id, target: node.id, kind: 'group', length: 72 + hub.items.length * 4, strength: 0.024 });
            });
        });

        if (state.showRelated) {
            const linked = new Set();

            (data.related || []).forEach((pair) => {
                const source = `k${pair.source}`;
                const target = `k${pair.target}`;

                if (!keywordNodes.some((node) => node.id === source) || !keywordNodes.some((node) => node.id === target)) {
                    return;
                }

                const key = `${source}:${target}`;

                if (linked.has(key)) return;

                linked.add(key);
                links.push({ source, target, kind: 'related', length: 118, strength: 0.006 });
            });
        }

        state.nodes = nodes;
        state.links = links;
        state.groups = groups;
        state.nodeById = new Map(nodes.map((node) => [node.id, node]));

        simulate(nodes, links, 320);
    };

    const simulate = (nodes, links, iterations) => {
        const random = createRandom(4242);
        const byId = new Map(nodes.map((node) => [node.id, node]));

        for (let step = 0; step < iterations; step += 1) {
            const alpha = 1 - (step / iterations) * 0.55;

            for (let i = 0; i < nodes.length; i += 1) {
                const first = nodes[i];

                for (let j = i + 1; j < nodes.length; j += 1) {
                    const second = nodes[j];
                    let dx = first.x - second.x;
                    let dy = first.y - second.y;
                    let squared = dx * dx + dy * dy;

                    if (squared < 1) {
                        dx = random() - 0.5;
                        dy = random() - 0.5;
                        squared = 1;
                    }

                    const distance = Math.sqrt(squared);

                    if (distance > 560) continue;

                    const ownsHub = first.type === 'hub' || second.type === 'hub';
                    const ownsRoot = first.type === 'root' || second.type === 'root';
                    const charge = ownsRoot ? 7000 : ownsHub ? 3200 : 1400;
                    const minimum = first.r + second.r + 14;

                    let force = (charge * alpha) / squared;

                    if (distance < minimum) {
                        force += (minimum - distance) * 0.18;
                    }

                    const fx = (dx / distance) * force;
                    const fy = (dy / distance) * force;

                    first.vx += fx;
                    first.vy += fy;
                    second.vx -= fx;
                    second.vy -= fy;
                }
            }

            links.forEach((link) => {
                const source = byId.get(link.source);
                const target = byId.get(link.target);

                if (!source || !target) return;

                const dx = target.x - source.x;
                const dy = target.y - source.y;
                const distance = Math.hypot(dx, dy) || 0.01;
                const force = (distance - link.length) * link.strength * alpha;
                const fx = (dx / distance) * force;
                const fy = (dy / distance) * force;

                source.vx += fx;
                source.vy += fy;
                target.vx -= fx;
                target.vy -= fy;
            });

            nodes.forEach((node) => {
                if (node.type === 'root') {
                    node.x = 0;
                    node.y = 0;
                    node.vx = 0;
                    node.vy = 0;
                    return;
                }

                node.vx -= node.x * 0.0014;
                node.vy -= node.y * 0.0014;
                node.vx *= 0.72;
                node.vy *= 0.72;

                node.x += clamp(node.vx, -18, 18);
                node.y += clamp(node.vy, -18, 18);
            });
        }
    };

    const render = () => {
        linkLayer.replaceChildren();
        nodeLayer.replaceChildren();

        state.links.forEach((link) => {
            const line = svgElement('line', {
                stroke: link.kind === 'bridge' ? '#f6a21a' : link.kind === 'related' ? '#818cf8' : '#cbd5e1',
                'stroke-width': link.kind === 'bridge' ? 1.6 : 1,
                'stroke-linecap': 'round',
                'stroke-dasharray': link.kind === 'related' ? '4 4' : null,
                'vector-effect': 'non-scaling-stroke',
            });

            if (link.kind === 'related') {
                line.style.opacity = '0.45';
                link.baseOpacity = 0.45;
            } else if (link.kind === 'bridge') {
                line.style.opacity = '0.7';
                link.baseOpacity = 0.7;
            } else {
                line.style.opacity = '0.8';
                link.baseOpacity = 0.8;
            }

            link.el = line;
            linkLayer.append(line);
        });

        state.nodes.forEach((node) => {
            const group = svgElement('g', {
                'data-node-id': node.id,
                tabindex: node.type === 'keyword' ? '0' : '-1',
                role: node.type === 'keyword' ? 'button' : 'presentation',
                'aria-label': node.label,
            });
            group.style.cursor = 'pointer';
            group.style.outline = 'none';

            const status = node.type === 'keyword' ? STATUS_STYLES[node.keyword.status] || STATUS_STYLES.planned : null;

            const hitArea = svgElement('circle', {
                r: node.r + (node.type === 'keyword' ? 9 : 5),
                fill: 'transparent',
            });

            const circle = svgElement('circle', {
                r: node.r,
                fill: node.type === 'root' ? '#0b2949' : node.type === 'hub' ? '#fff' : status.fill,
                stroke: node.type === 'root' ? '#0b2949' : node.type === 'hub' ? '#f6a21a' : status.stroke,
                'stroke-width': node.type === 'hub' ? 2 : 1.4,
            });

            if (node.type === 'root') {
                circle.setAttribute('fill-opacity', '0.95');
            }

            const title = svgElement('title');
            title.textContent = node.type === 'keyword'
                ? `${node.keyword.keyword}\n${node.keyword.statusLabel} · اولویت ${fa(node.keyword.priority)}`
                : node.type === 'hub'
                    ? `${node.label} (${fa(node.items.length)} کلمه)`
                    : node.label;

            const label = svgElement('text', {
                'text-anchor': 'middle',
                'font-family': "'Vazirmatn Variable', ui-sans-serif, system-ui, sans-serif",
                direction: 'rtl',
                fill: node.type === 'root' ? '#ffffff' : node.type === 'hub' ? '#0b2949' : '#475569',
                'font-size': node.type === 'root' ? 11.5 : node.type === 'hub' ? 12 : 10.5,
                'font-weight': node.type === 'keyword' ? 500 : 700,
                'pointer-events': node.type === 'root' ? 'none' : 'auto',
            });

            if (node.type === 'keyword') {
                label.setAttribute('stroke', '#ffffff');
                label.setAttribute('stroke-width', '3');
                label.setAttribute('paint-order', 'stroke');
            }

            if (node.type === 'root') {
                label.setAttribute('dy', '0.35em');
                label.textContent = node.label;
            } else {
                label.setAttribute('dy', node.r + (node.type === 'hub' ? 15 : 12));
                label.textContent = shorten(node.label, node.type === 'hub' ? 22 : 28);
            }

            node.defaultStroke = circle.getAttribute('stroke');
            node.defaultStrokeWidth = circle.getAttribute('stroke-width');
            node.circle = circle;
            node.labelElement = label;
            node.el = group;

            group.append(hitArea, circle, label, title);
            nodeLayer.append(group);
        });

        positionAll();
        applyTransform();
        updateCount();
        restoreHighlight();
    };

    const positionAll = () => {
        state.nodes.forEach((node) => {
            node.el?.setAttribute('transform', `translate(${node.x.toFixed(2)} ${node.y.toFixed(2)})`);
        });

        state.links.forEach((link) => {
            const source = state.nodeById.get(link.source);
            const target = state.nodeById.get(link.target);

            if (!source || !target || !link.el) return;

            link.el.setAttribute('x1', source.x.toFixed(2));
            link.el.setAttribute('y1', source.y.toFixed(2));
            link.el.setAttribute('x2', target.x.toFixed(2));
            link.el.setAttribute('y2', target.y.toFixed(2));
        });
    };

    const applyTransform = () => {
        const { x, y, k } = state.transform;
        viewport.setAttribute('transform', `translate(${x} ${y}) scale(${k})`);
        updateLabels();
    };

    const updateLabels = () => {
        const scale = state.transform.k;

        state.nodes.forEach((node) => {
            if (!node.labelElement) return;

            if (node.type !== 'keyword') {
                node.labelElement.style.display = '';
                return;
            }

            const show = state.showLabels
                || scale >= 1.15
                || node.id === state.hoveredId
                || node.id === state.selectedId;

            node.labelElement.style.display = show ? '' : 'none';
        });
    };

    const updateCount = () => {
        if (!countElement) return;

        const keywordCount = state.nodes.filter((node) => node.type === 'keyword').length;
        countElement.textContent = `${fa(keywordCount)} کلمه · ${fa(state.groups.size)} گروه · ${fa(state.links.length)} ارتباط`;
    };

    const neighborsOf = (id) => {
        const ids = new Set([id]);

        state.links.forEach((link) => {
            if (link.source === id) ids.add(link.target);
            if (link.target === id) ids.add(link.source);
        });

        return ids;
    };

    const highlight = (id) => {
        const active = id ? neighborsOf(id) : null;

        state.nodes.forEach((node) => {
            if (!node.el) return;
            node.el.style.opacity = !active || active.has(node.id) ? '1' : '0.16';
        });

        state.links.forEach((link) => {
            if (!link.el) return;
            const isActive = !active || link.source === id || link.target === id;
            link.el.style.opacity = isActive ? String(link.baseOpacity) : '0.05';
        });
    };

    const restoreHighlight = () => {
        const focusId = state.hoveredId || state.selectedId;

        highlight(focusId);

        state.nodes.forEach((node) => {
            if (!node.circle || node.type !== 'keyword') return;

            const selected = node.id === state.selectedId;
            node.circle.setAttribute('stroke', selected ? '#0b2949' : node.defaultStroke);
            node.circle.setAttribute('stroke-width', selected ? '3' : node.defaultStrokeWidth);
        });

        updateLabels();
    };

    const selectNode = (node, options = {}) => {
        state.selectedId = node?.id || null;

        if (node?.type === 'keyword') {
            showKeywordBrief(node.keyword);
        } else if (node?.type === 'hub') {
            showGroupBrief(node);
        } else {
            showEmptyBrief();
        }

        restoreHighlight();

        if (options.focus && node) {
            focusNode(node);
        }

        if (panel && window.innerWidth < 1280) {
            panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }
    };

    const clearSelection = () => {
        state.selectedId = null;
        showEmptyBrief();
        restoreHighlight();
    };

    const showEmptyBrief = () => {
        emptyPanel?.classList.remove('hidden');
        contentPanel?.classList.add('hidden');
        groupPanel?.classList.add('hidden');
    };

    const fillChips = (container, values) => {
        if (!container) return;
        container.replaceChildren();

        values.forEach((value) => {
            if (!chipTemplate) return;
            const chip = chipTemplate.content.firstElementChild.cloneNode(true);
            chip.textContent = value;
            container.append(chip);
        });
    };

    const showKeywordBrief = (keyword) => {
        if (!contentPanel) return;

        emptyPanel?.classList.add('hidden');
        groupPanel?.classList.add('hidden');
        contentPanel.classList.remove('hidden');

        const setText = (selector, value) => {
            const element = contentPanel.querySelector(selector);
            if (element) element.textContent = value;
        };

        const toggle = (selector, visible) => {
            contentPanel.querySelector(selector)?.classList.toggle('hidden', !visible);
        };

        setText('[data-brief-keyword]', keyword.keyword);

        const statusBadge = contentPanel.querySelector('[data-brief-status]');
        const statusStyle = STATUS_STYLES[keyword.status] || STATUS_STYLES.planned;

        if (statusBadge) {
            statusBadge.className = `badge border text-[10px] ${statusStyle.badge}`;
            statusBadge.textContent = keyword.statusLabel;
        }

        setText('[data-brief-priority]', `اولویت ${fa(keyword.priority)}`);
        setText('[data-brief-role]', keyword.roleLabel);
        setText('[data-brief-page]', keyword.targetPage || '—');
        setText('[data-brief-page-type]', keyword.pageTypeLabel || '—');
        setText('[data-brief-position]', keyword.position ? fa(keyword.position) : '—');
        setText('[data-brief-open-tasks]', `${fa(keyword.openTasksCount)} تسک`);

        const titleWrap = '[data-brief-title-wrap]';
        toggle(titleWrap, Boolean(keyword.contentTitle));
        setText('[data-brief-title]', keyword.contentTitle || '');

        const secondary = splitPhrases(keyword.secondaryKeywords);
        toggle('[data-brief-secondary-wrap]', secondary.length > 0);
        fillChips(contentPanel.querySelector('[data-brief-secondary]'), secondary);

        const internal = splitPhrases(keyword.internalLinks);
        toggle('[data-brief-internal-wrap]', internal.length > 0);
        fillChips(contentPanel.querySelector('[data-brief-internal]'), internal);

        toggle('[data-brief-notes-wrap]', Boolean(keyword.notes));
        setText('[data-brief-notes]', keyword.notes || '');

        const taskList = contentPanel.querySelector('[data-brief-tasks]');
        const tasksEmpty = contentPanel.querySelector('[data-brief-tasks-empty]');
        taskList?.replaceChildren();

        (keyword.tasks || []).forEach((task) => {
            if (!taskTemplate || !taskList) return;

            const item = taskTemplate.content.firstElementChild.cloneNode(true);
            const status = item.querySelector('[data-task-status]');
            const category = item.querySelector('[data-task-category]');
            const due = item.querySelector('[data-task-due]');

            item.querySelector('[data-task-title]').textContent = task.title;

            if (status) {
                status.className = `badge border text-[10px] ${TASK_STATUS_CLASSES[task.status] || TASK_STATUS_CLASSES.todo}`;
                status.textContent = task.statusLabel;
            }

            if (category) {
                category.textContent = task.categoryLabel;
            }

            if (due) {
                if (task.dueDateLabel) {
                    due.textContent = task.dueDateLabel;
                    due.classList.toggle('!border-rose-200', Boolean(task.isOverdue));
                    due.classList.toggle('!bg-rose-50', Boolean(task.isOverdue));
                    due.classList.toggle('!text-rose-700', Boolean(task.isOverdue));
                } else {
                    due.classList.add('hidden');
                }
            }

            taskList.append(item);
        });

        tasksEmpty?.classList.toggle('hidden', (keyword.tasks || []).length > 0);

        const targetLink = contentPanel.querySelector('[data-brief-target-url]');
        if (targetLink) {
            if (keyword.targetUrl) {
                targetLink.href = keyword.targetUrl;
                targetLink.classList.remove('hidden');
                targetLink.classList.add('inline-flex');
            } else {
                targetLink.classList.add('hidden');
                targetLink.classList.remove('inline-flex');
            }
        }

        const tableLink = contentPanel.querySelector('[data-brief-table]');
        if (tableLink && keyword.tableUrl) {
            tableLink.href = keyword.tableUrl;
        }

        const taskForm = contentPanel.querySelector('[data-brief-task-form]');
        if (taskForm) {
            taskForm.classList.toggle('hidden', !keyword.canCreateTask || !keyword.taskUrl);
            if (keyword.taskUrl) taskForm.action = keyword.taskUrl;
        }

        contentPanel.dataset.keywordId = String(keyword.id);
    };

    const showGroupBrief = (hub) => {
        if (!groupPanel) return;

        emptyPanel?.classList.add('hidden');
        contentPanel?.classList.add('hidden');
        groupPanel.classList.remove('hidden');

        groupPanel.querySelector('[data-group-title]').textContent = hub.label;
        groupPanel.querySelector('[data-group-count]').textContent = `${fa(hub.items.length)} کلمه در این گروه`;
        groupPanel.querySelector('[data-group-mode]').textContent = {
            page: 'گروه‌بندی بر اساس صفحه هدف',
            page_type: 'گروه‌بندی بر اساس نوع صفحه',
            role: 'گروه‌بندی بر اساس نقش کلمه',
        }[state.mode];

        const list = groupPanel.querySelector('[data-group-keywords]');
        list?.replaceChildren();

        hub.items.forEach((node) => {
            if (!keywordChipTemplate || !list) return;

            const chip = keywordChipTemplate.content.firstElementChild.cloneNode(true);
            chip.textContent = node.label;
            chip.dataset.keywordNode = node.id;
            list.append(chip);
        });
    };

    const focusNode = (node) => {
        const rect = svg.getBoundingClientRect();
        const scale = 1.35;

        state.transform = {
            k: scale,
            x: rect.width / 2 - node.x * scale,
            y: rect.height / 2 - node.y * scale,
        };

        applyTransform();
    };

    const fitView = () => {
        const bounds = state.nodes.reduce(
            (accumulator, node) => ({
                minX: Math.min(accumulator.minX, node.x - node.r),
                maxX: Math.max(accumulator.maxX, node.x + node.r),
                minY: Math.min(accumulator.minY, node.y - node.r),
                maxY: Math.max(accumulator.maxY, node.y + node.r),
            }),
            { minX: Infinity, maxX: -Infinity, minY: Infinity, maxY: -Infinity },
        );

        const rect = svg.getBoundingClientRect();
        const width = Math.max(1, bounds.maxX - bounds.minX);
        const height = Math.max(1, bounds.maxY - bounds.minY);
        const padding = 56;
        const scale = clamp(
            Math.min((rect.width - padding) / width, (rect.height - padding) / height),
            0.22,
            1.5,
        );

        state.transform = {
            k: scale,
            x: rect.width / 2 - ((bounds.minX + bounds.maxX) / 2) * scale,
            y: rect.height / 2 - ((bounds.minY + bounds.maxY) / 2) * scale,
        };

        applyTransform();
    };

    const rebuild = () => {
        state.selectedId = null;
        state.hoveredId = null;
        buildModel();
        render();
        fitView();
        showEmptyBrief();
    };

    const zoomAt = (clientX, clientY, factor) => {
        const rect = svg.getBoundingClientRect();
        const x = clientX - rect.left;
        const y = clientY - rect.top;
        const previous = state.transform;
        const nextScale = clamp(previous.k * factor, 0.25, 3.2);
        const worldX = (x - previous.x) / previous.k;
        const worldY = (y - previous.y) / previous.k;

        state.transform = {
            k: nextScale,
            x: x - worldX * nextScale,
            y: y - worldY * nextScale,
        };

        applyTransform();
    };

    const toWorld = (clientX, clientY) => {
        const rect = svg.getBoundingClientRect();

        return {
            x: (clientX - rect.left - state.transform.x) / state.transform.k,
            y: (clientY - rect.top - state.transform.y) / state.transform.k,
        };
    };

    const settle = (node) => {
        const start = performance.now();
        let frame = 0;

        const step = (now) => {
            const elapsed = now - start;

            for (let index = 0; index < 3; index += 1) {
                const links = state.links.filter((link) => link.source === node.id || link.target === node.id);

                links.forEach((link) => {
                    const source = state.nodeById.get(link.source);
                    const target = state.nodeById.get(link.target);

                    if (!source || !target) return;

                    const dx = target.x - source.x;
                    const dy = target.y - source.y;
                    const distance = Math.hypot(dx, dy) || 0.01;
                    const force = (distance - link.length) * link.strength * 2.2;
                    const fx = (dx / distance) * force;
                    const fy = (dy / distance) * force;

                    if (source !== node) {
                        source.vx = (source.vx + fx) * 0.8;
                        source.vy = (source.vy + fy) * 0.8;
                        source.x += source.vx;
                        source.y += source.vy;
                    }

                    if (target !== node) {
                        target.vx = (target.vx - fx) * 0.8;
                        target.vy = (target.vy - fy) * 0.8;
                        target.x += target.vx;
                        target.y += target.vy;
                    }
                });
            }

            positionAll();
            frame += 1;

            if (elapsed < 420 && frame < 60) {
                requestAnimationFrame(step);
            }
        };

        requestAnimationFrame(step);
    };

    svg.addEventListener('pointerdown', (event) => {
        const nodeElement = event.target.closest?.('[data-node-id]');
        const node = nodeElement ? state.nodeById.get(nodeElement.dataset.nodeId) : null;

        if (node && node.type !== 'root') {
            state.drag = {
                node,
                startX: event.clientX,
                startY: event.clientY,
                moved: false,
            };

            node.el.style.cursor = 'grabbing';
            svg.setPointerCapture(event.pointerId);
            event.preventDefault();
            return;
        }

        if (node?.type === 'root') {
            svg.setPointerCapture(event.pointerId);
        }

        state.pan = {
            startX: event.clientX,
            startY: event.clientY,
            originX: state.transform.x,
            originY: state.transform.y,
            moved: false,
        };

        svg.style.cursor = 'grabbing';
        svg.setPointerCapture(event.pointerId);
        event.preventDefault();
    });

    svg.addEventListener('pointermove', (event) => {
        if (state.drag) {
            const { node } = state.drag;
            const moved = Math.hypot(event.clientX - state.drag.startX, event.clientY - state.drag.startY);

            if (moved > 2) state.drag.moved = true;

            const point = toWorld(event.clientX, event.clientY);
            node.x = point.x;
            node.y = point.y;
            node.vx = 0;
            node.vy = 0;

            node.el.setAttribute('transform', `translate(${node.x.toFixed(2)} ${node.y.toFixed(2)})`);

            state.links.forEach((link) => {
                if (link.source !== node.id && link.target !== node.id) return;

                const source = state.nodeById.get(link.source);
                const target = state.nodeById.get(link.target);

                if (!source || !target) return;

                link.el.setAttribute('x1', source.x.toFixed(2));
                link.el.setAttribute('y1', source.y.toFixed(2));
                link.el.setAttribute('x2', target.x.toFixed(2));
                link.el.setAttribute('y2', target.y.toFixed(2));
            });

            return;
        }

        if (state.pan) {
            if (Math.hypot(event.clientX - state.pan.startX, event.clientY - state.pan.startY) > 3) {
                state.pan.moved = true;
            }

            state.transform.x = state.pan.originX + (event.clientX - state.pan.startX);
            state.transform.y = state.pan.originY + (event.clientY - state.pan.startY);
            applyTransform();
        }
    });

    const endPointer = (event) => {
        if (state.drag) {
            const { node, moved } = state.drag;

            node.el.style.cursor = 'pointer';
            state.drag = null;

            // The click event fires after pointerup and can be retargeted to the
            // svg root because of pointer capture; keep it from clearing the brief.
            state.suppressClick = true;
            window.setTimeout(() => {
                state.suppressClick = false;
            }, 250);

            if (moved) {
                settle(node);
            } else {
                selectNode(node);
            }
        }

        if (state.pan) {
            if (state.pan.moved) {
                state.suppressClick = true;
                window.setTimeout(() => {
                    state.suppressClick = false;
                }, 250);
            }

            state.pan = null;
            svg.style.cursor = 'grab';
        }

        if (svg.hasPointerCapture?.(event.pointerId)) {
            svg.releasePointerCapture(event.pointerId);
        }
    };

    svg.addEventListener('pointerup', endPointer);
    svg.addEventListener('pointercancel', endPointer);

    svg.addEventListener('pointerover', (event) => {
        const nodeElement = event.target.closest?.('[data-node-id]');
        const node = nodeElement ? state.nodeById.get(nodeElement.dataset.nodeId) : null;

        if (!node) return;

        state.hoveredId = node.id;
        highlight(node.id);
        updateLabels();
    });

    svg.addEventListener('pointerout', (event) => {
        if (event.relatedTarget?.closest?.('[data-node-id]')) return;

        state.hoveredId = null;
        restoreHighlight();
    });

    svg.addEventListener('click', (event) => {
        if (state.suppressClick) {
            state.suppressClick = false;
            return;
        }

        if (event.target.closest?.('[data-node-id]')) return;
        clearSelection();
    });

    svg.addEventListener('keydown', (event) => {
        if (event.key !== 'Enter' && event.key !== ' ') return;

        const nodeElement = event.target.closest?.('[data-node-id]');
        const node = nodeElement ? state.nodeById.get(nodeElement.dataset.nodeId) : null;

        if (!node) return;

        event.preventDefault();
        selectNode(node);
    });

    svg.addEventListener(
        'wheel',
        (event) => {
            event.preventDefault();
            const factor = Math.exp(-event.deltaY * 0.0015);
            zoomAt(event.clientX, event.clientY, factor);
        },
        { passive: false },
    );

    zoomInButton?.addEventListener('click', () => {
        const rect = svg.getBoundingClientRect();
        zoomAt(rect.left + rect.width / 2, rect.top + rect.height / 2, 1.25);
    });

    zoomOutButton?.addEventListener('click', () => {
        const rect = svg.getBoundingClientRect();
        zoomAt(rect.left + rect.width / 2, rect.top + rect.height / 2, 0.8);
    });

    fitButton?.addEventListener('click', fitView);
    resetButton?.addEventListener('click', rebuild);

    groupSelect?.addEventListener('change', () => {
        if (!GROUP_MODES.includes(groupSelect.value)) return;
        state.mode = groupSelect.value;
        rebuild();
    });

    relatedToggle?.addEventListener('change', () => {
        state.showRelated = relatedToggle.checked;
        rebuild();
    });

    labelsToggle?.addEventListener('change', () => {
        state.showLabels = labelsToggle.checked;
        updateLabels();
    });

    groupPanel?.addEventListener('click', (event) => {
        const chip = event.target.closest('[data-keyword-node]');

        if (!chip) return;

        const node = state.nodeById.get(chip.dataset.keywordNode);

        if (node) selectNode(node, { focus: true });
    });

    const copyButton = contentPanel?.querySelector('[data-brief-copy]');
    const copyLabel = contentPanel?.querySelector('[data-brief-copy-label]');

    copyButton?.addEventListener('click', async () => {
        const keywordId = contentPanel?.dataset.keywordId;
        const keyword = data.nodes.find((node) => String(node.id) === keywordId);

        if (!keyword) return;

        const secondary = splitPhrases(keyword.secondaryKeywords);
        const internal = splitPhrases(keyword.internalLinks);
        const lines = [
            `بریف کلمه: ${keyword.keyword}`,
            `وضعیت: ${keyword.statusLabel}`,
            `اولویت: ${fa(keyword.priority)}`,
            `نقش: ${keyword.roleLabel}`,
            `نوع صفحه: ${keyword.pageTypeLabel}`,
            `صفحه هدف: ${keyword.targetPage || '—'}`,
            keyword.position ? `رتبه: ${fa(keyword.position)}` : null,
            keyword.contentTitle ? `عنوان مقاله: ${keyword.contentTitle}` : null,
            secondary.length ? `کلمات فرعی: ${secondary.join('، ')}` : null,
            internal.length ? `لینک‌های داخلی: ${internal.join('، ')}` : null,
            keyword.notes ? `یادداشت: ${keyword.notes}` : null,
        ].filter(Boolean);

        const text = lines.join('\n');

        try {
            await navigator.clipboard.writeText(text);
        } catch (error) {
            const textarea = document.createElement('textarea');
            textarea.value = text;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.append(textarea);
            textarea.select();
            document.execCommand('copy');
            textarea.remove();
        }

        if (copyLabel) {
            const original = copyLabel.textContent;
            copyLabel.textContent = 'کپی شد';
            window.setTimeout(() => {
                copyLabel.textContent = original;
            }, 1800);
        }
    });

    let resizeTimer = null;
    window.addEventListener('resize', () => {
        window.clearTimeout(resizeTimer);
        resizeTimer = window.setTimeout(() => {
            if (state.nodes.length === 0) return;
            applyTransform();
        }, 150);
    });

    buildModel();
    render();
    fitView();
}
