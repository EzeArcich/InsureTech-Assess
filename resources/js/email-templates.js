const editor = document.querySelector('#template-editor');
if (editor) {
    const form = document.querySelector('#email-template-form');
    const body = document.querySelector('#template-body');
    let range;
    const sync = () => { body.value = editor.innerHTML; };
    const remember = () => {
        const selection = window.getSelection();
        if (selection.rangeCount && editor.contains(selection.anchorNode)) range = selection.getRangeAt(0).cloneRange();
    };
    ['keyup', 'mouseup', 'input'].forEach(event => editor.addEventListener(event, () => { remember(); sync(); }));
    const insert = (node, wrap = false) => {
        editor.focus();
        if (!range || !editor.contains(range.commonAncestorContainer)) { range = document.createRange(); range.selectNodeContents(editor); range.collapse(false); }
        if (wrap) {
            const content = range.extractContents();
            if (node.tagName === 'UL') { const li = document.createElement('li'); li.append(content); if (!li.textContent) li.textContent = 'Elemento'; node.append(li); }
            else { node.append(content); if (!node.textContent) node.textContent = 'Texto'; }
        } else range.deleteContents();
        range.insertNode(node); range.setStartAfter(node); range.collapse(true);
        const selection = window.getSelection(); selection.removeAllRanges(); selection.addRange(range); sync();
    };
    form.querySelectorAll('[data-format]').forEach(button => button.addEventListener('click', () => insert(document.createElement(button.dataset.format), true)));
    form.querySelectorAll('[data-variable]').forEach(button => button.addEventListener('click', () => insert(document.createTextNode(`{{ ${button.dataset.variable} }}`))));
    editor.addEventListener('paste', event => { event.preventDefault(); insert(document.createTextNode(event.clipboardData.getData('text/plain'))); });
    form.addEventListener('submit', sync);
    sync();
}
