document.getElementById('imagem').onchange = function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            let preview = document.getElementById('preview');
            if (!preview) {
                preview = document.createElement('img');
                preview.id = 'preview';
                preview.width = 100;
                preview.height = 100;
                e.target.parentNode.appendChild(preview);
            }
            preview.src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};