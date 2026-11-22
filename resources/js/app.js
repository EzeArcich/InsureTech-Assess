import './bootstrap';
import * as bootstrap from 'bootstrap';

document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(el => {
  new bootstrap.Tooltip(el);
});
import './tables';

const photoViewer = document.querySelector('#inspection-photo-viewer');
if (photoViewer) {
  const photos = [...document.querySelectorAll('[data-photo-viewer]')];
  let currentPhoto = 0;
  const showPhoto = index => {
    currentPhoto = (index + photos.length) % photos.length;
    const image = photoViewer.querySelector('[data-photo-image]');
    image.src = photos[currentPhoto].href;
    image.alt = photos[currentPhoto].querySelector('img').alt;
    photoViewer.querySelector('[data-photo-count]').textContent = `${currentPhoto + 1} / ${photos.length}`;
  };
  photoViewer.addEventListener('show.bs.modal', event => showPhoto(photos.indexOf(event.relatedTarget)));
  photoViewer.querySelector('[data-photo-previous]').addEventListener('click', () => showPhoto(currentPhoto - 1));
  photoViewer.querySelector('[data-photo-next]').addEventListener('click', () => showPhoto(currentPhoto + 1));
}

const photoUpload = document.querySelector('[data-inspection-upload]');
if (photoUpload && typeof DataTransfer !== 'undefined') {
  const input = photoUpload.querySelector('input[type=file]');
  const dropzone = photoUpload.querySelector('[data-photo-dropzone]');
  const previews = photoUpload.querySelector('[data-photo-previews]');
  const status = photoUpload.querySelector('[aria-live]');
  let selectedFiles = [];
  let previewUrls = [];
  const render = message => {
    const transfer = new DataTransfer();
    selectedFiles.forEach(file => transfer.items.add(file));
    input.files = transfer.files;
    previewUrls.forEach(url => URL.revokeObjectURL(url));
    previewUrls = [];
    previews.replaceChildren();
    selectedFiles.forEach((file, index) => {
      const card = document.createElement('div'); card.className = 'inspection-upload__preview';
      const image = document.createElement('img'); image.alt = ''; image.src = URL.createObjectURL(file); previewUrls.push(image.src);
      const name = document.createElement('span'); name.textContent = file.name; name.title = file.name;
      const remove = document.createElement('button'); remove.type = 'button'; remove.className = 'inspection-upload__remove'; remove.setAttribute('aria-label', `Quitar ${file.name}`); remove.textContent = '×';
      remove.addEventListener('click', () => { selectedFiles.splice(index, 1); render(); });
      card.append(image, name, remove); previews.append(card);
    });
    status.textContent = message || (selectedFiles.length ? `${selectedFiles.length} foto${selectedFiles.length === 1 ? '' : 's'} lista${selectedFiles.length === 1 ? '' : 's'} para guardar` : '');
  };
  const addFiles = files => {
    let rejected = 0;
    [...files].forEach(file => {
      if (!['image/jpeg', 'image/png', 'image/webp'].includes(file.type) || file.size > 10 * 1024 * 1024 || selectedFiles.length >= 20) { rejected++; return; }
      selectedFiles.push(file);
    });
    render(rejected ? `${rejected} archivo(s) no se agregaron: revisá el formato, el tamaño y el límite de 20 fotos.` : '');
  };
  input.addEventListener('change', () => addFiles(input.files));
  ['dragenter', 'dragover'].forEach(type => dropzone.addEventListener(type, event => { event.preventDefault(); dropzone.classList.add('is-dragging'); }));
  ['dragleave', 'drop'].forEach(type => dropzone.addEventListener(type, event => { event.preventDefault(); dropzone.classList.remove('is-dragging'); }));
  dropzone.addEventListener('drop', event => addFiles(event.dataTransfer.files));
}
