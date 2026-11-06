<section {{ $attributes->merge(['class' => 'claims-table-panel']) }}>
    <div class="claims-table-actions">
        {{ $actions ?? '' }}
    </div>
    <div class="claims-table-scroll">
        {{ $slot }}
    </div>
</section>
