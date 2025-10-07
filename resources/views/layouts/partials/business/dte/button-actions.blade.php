<div class="mt-4 flex flex-col items-center justify-center gap-4 px-4 sm:flex-row">
    <x-button type="submit" typeButton="primary" icon="file-symlink" text="Generar documento" class="w-full sm:w-auto {{(($dte['status'] ?? null) === 'template') ? 'hidden' : ''}}"
        name="action" value="generate" id="generate-button" />
    <x-button type="submit" name="action" value="draft"
        text="{{ isset($dte['id']) ? 'Actualizar' : 'Guardar como' }} borrador" typeButton="secondary"
    class="w-full sm:w-auto {{(($dte['status'] ?? null) === 'template') ? 'hidden' : ''}}" icon="save" id="draft-button" />
    <x-button type="submit" name="action" value="template"
        text="{{ isset($dte['id']) ? 'Actualizar' : 'Guardar' }} plantilla" typeButton="success"
    class="w-full sm:w-auto {{(($dte['status'] ?? null) === 'template') ? '' : 'hidden'}}" icon="files" id="template-button" />
</div>
