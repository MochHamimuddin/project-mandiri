@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const config = @json($alertConfig);

    // Only show if there's a message
    if (config.message) {
        Swal.fire({
            icon: config.type,
            title: config.title,
            html: config.message,
            timer: config.timer,
            timerProgressBar: config.timer && config.timer > 0,
            showConfirmButton: config.showConfirmButton,
            showCancelButton: config.showCancelButton,
            confirmButtonText: config.confirmButtonText,
            confirmButtonColor: config.confirmButtonColor,
            cancelButtonColor: config.cancelButtonColor,
            position: config.position,
            background: config.background,
            iconColor: config.iconColor,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });
    }
});
</script>
@endpush
