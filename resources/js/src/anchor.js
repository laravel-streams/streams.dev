import AnchorJS from 'anchor-js';

const anchors = new AnchorJS();

anchors.options = {
    placement: 'left',
    visible: 'always',
    truncate: 64,
    icon: ''
};

anchors.add('.o-doc-body h1, .o-doc-body h2, .o-doc-body h3, .o-doc-body h4');