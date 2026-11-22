<section class="mb-4" aria-labelledby="inspection-photos-title">
    <h3 class="h5" id="inspection-photos-title">Fotos de la pericia</h3>
    <div class="inspection-upload" data-inspection-upload>
        <div class="inspection-upload__dropzone" data-photo-dropzone>
            <span class="inspection-upload__icon" aria-hidden="true"><i class="fas fa-cloud-upload-alt"></i></span>
            <strong>Arrastrá las fotos de la pericia acá</strong>
            <span>o elegilas desde tu dispositivo</span>
            <label class="btn btn-outline-primary mt-2" for="inspection-photos">Seleccionar fotos</label>
            <input class="visually-hidden" id="inspection-photos" type="file" name="inspection_photos[]" multiple accept="image/jpeg,image/png,image/webp" aria-describedby="inspection-photos-help inspection-upload-status">
            <small id="inspection-photos-help">JPG, PNG o WebP · Hasta 20 imágenes de 10 MB cada una</small>
        </div>
        <p class="inspection-upload__status" id="inspection-upload-status" aria-live="polite"></p>
        <div class="inspection-upload__previews" data-photo-previews></div>
    </div>
    @if($siniestro->inspectionPhotos->isNotEmpty())
        <div class="d-flex flex-wrap gap-2 mt-3">
            @foreach($siniestro->inspectionPhotos as $photo)
                <a href="{{ route('claims.photos.show', [$siniestro->id, $photo->id]) }}" data-photo-viewer data-bs-toggle="modal" data-bs-target="#inspection-photo-viewer" aria-label="Abrir foto de la pericia {{ $loop->iteration }}"><img src="{{ route('claims.photos.show', [$siniestro->id, $photo->id]) }}" alt="Foto de la pericia {{ $loop->iteration }}" loading="lazy" style="width: 140px; height: 100px; object-fit: cover; border-radius: 6px"></a>
            @endforeach
        </div>
        <div class="modal fade" id="inspection-photo-viewer" tabindex="-1" aria-labelledby="inspection-photo-viewer-title" aria-hidden="true"><div class="modal-dialog modal-xl modal-dialog-centered"><div class="modal-content bg-dark text-white"><div class="modal-header"><h4 class="modal-title fs-5" id="inspection-photo-viewer-title">Fotos de la pericia</h4><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Cerrar"></button></div><div class="modal-body text-center"><img data-photo-image src="" alt="" style="max-width: 100%; max-height: 70vh; object-fit: contain"></div><div class="modal-footer justify-content-between"><button type="button" class="btn btn-outline-light" data-photo-previous>Anterior</button><span data-photo-count></span><button type="button" class="btn btn-outline-light" data-photo-next>Siguiente</button></div></div></div></div>
    @else
        <p class="text-muted mt-2">Todavía no hay fotos de la pericia.</p>
    @endif
</section>
