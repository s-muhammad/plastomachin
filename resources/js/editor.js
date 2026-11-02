import {
    Alignment,
    Autoformat,
    BlockQuote,
    Bold,
    ClassicEditor,
    Essentials,
    Heading,
    Image,
    ImageCaption,
    ImageInsert,
    ImageResize,
    ImageStyle,
    ImageTextAlternative,
    ImageToolbar,
    Italic,
    Link,
    List,
    ListProperties,
    Paragraph,
    PasteFromOffice,
    RemoveFormat,
    SourceEditing,
    Strikethrough,
    Underline,
} from 'ckeditor5';
import fa from 'ckeditor5/translations/fa.js';
import 'ckeditor5/ckeditor5.css';

const editorConfig = {
    licenseKey: 'GPL',
    plugins: [
        Essentials,
        Paragraph,
        Heading,
        Autoformat,
        Bold,
        Italic,
        Underline,
        Strikethrough,
        RemoveFormat,
        Link,
        List,
        ListProperties,
        BlockQuote,
        Alignment,
        PasteFromOffice,
        Image,
        ImageToolbar,
        ImageStyle,
        ImageResize,
        ImageCaption,
        ImageTextAlternative,
        ImageInsert,
        SourceEditing,
    ],
    language: {
        ui: 'fa',
        content: 'fa',
    },
    translations: [fa],
    placeholder: 'متن خود را اینجا بنویسید…',
    toolbar: [
        'undo',
        'redo',
        '|',
        'heading',
        '|',
        'bold',
        'italic',
        'underline',
        'strikethrough',
        'removeFormat',
        '|',
        'alignment',
        '|',
        'bulletedList',
        'numberedList',
        '|',
        'link',
        'blockQuote',
        'insertImage',
        '|',
        'sourceEditing',
    ],
    heading: {
        options: [
            { model: 'paragraph', title: 'پاراگراف', class: 'ck-heading_paragraph' },
            { model: 'heading2', view: 'h2', title: 'تیتر ۲', class: 'ck-heading_heading2' },
            { model: 'heading3', view: 'h3', title: 'تیتر ۳', class: 'ck-heading_heading3' },
            { model: 'heading4', view: 'h4', title: 'تیتر ۴', class: 'ck-heading_heading4' },
        ],
    },
    link: {
        addTargetToExternalLinks: true,
        defaultProtocol: 'https://',
    },
    list: {
        properties: {
            styles: true,
            startIndex: true,
            reversed: true,
        },
    },
    image: {
        toolbar: [
            'imageStyle:inline',
            'imageStyle:block',
            'imageStyle:side',
            '|',
            'toggleImageCaption',
            'imageTextAlternative',
            '|',
            'resizeImage',
        ],
        resizeOptions: [
            { name: 'resizeImage:original', value: null, label: 'اندازه‌ی اصلی' },
            { name: 'resizeImage:50', value: '50', label: '۵۰٪' },
            { name: 'resizeImage:75', value: '75', label: '۷۵٪' },
        ],
    },
};

const initEditor = (element) => {
    if (element.dataset.editorReady === 'true') {
        return;
    }

    element.dataset.editorReady = 'true';

    ClassicEditor.create(element, editorConfig).catch((error) => {
        element.dataset.editorReady = 'false';
        console.error('CKEditor initialization failed:', error);
    });
};

export function initEditors() {
    document.querySelectorAll('textarea[data-editor]').forEach(initEditor);
}
