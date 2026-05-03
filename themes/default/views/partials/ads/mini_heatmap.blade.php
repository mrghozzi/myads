<div class="mini-heatmap" title="Hourly Click Distribution (00:00 - 23:00)">
    @php
        $max = max($heatmap) ?: 1;
    @endphp
    <div style="display: flex; align-items: flex-end; gap: 1px; height: 30px; width: 100%; min-width: 120px;">
        @foreach($heatmap as $hour => $count)
            @php
                $height = $count > 0 ? ($count / $max) * 100 : 20; // Min 20% height for idle
                $bg = $count > 0 
                    ? 'rgba(255, 152, 0, ' . (0.4 + ($count / $max) * 0.6) . ')' 
                    : 'rgba(128, 128, 128, 0.1)'; // Subtle gray for both modes
            @endphp
            <div class="heatmap-bar" 
                 style="flex: 1; height: {{ $height }}%; background: {{ $bg }}; border-radius: 1px; cursor: help;"
                 data-bs-toggle="tooltip" 
                 data-bs-placement="top"
                 title="{{ sprintf('%02d:00', $hour) }}: {{ $count }} clicks">
            </div>
        @endforeach
    </div>
</div>

@once
<script>
    // Initialize tooltips for all instances on the page
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl)
        })
    });
</script>
@endonce
