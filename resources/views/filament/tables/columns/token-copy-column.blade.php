<div x-data="{ copied: false, registerUrl: '{{$getState()}}' }">
    <button
        @click.prevent="copyToClipboard"
        class="px-4 py-2 bg-gray-100 rounded text-grey-500 dark:text-white hover:bg-gray-200 dark:bg-gray-300/5 dark:hover:bg-gray-300/10 focus:outline-none"
    >
        <span x-text="copied ? '{{__('column.copied')}}!' : '{{__('column.copy')}}'"></span>
    </button>
</div>
<script>
    function copyToClipboard() {
        const baseUrl = @json(url('/list-invoices'));
        const copyText = baseUrl + '/' + this.registerUrl;
        const textArea = document.createElement('textarea');
        textArea.value = copyText;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        this.copied = true;

        setTimeout(() => {
            this.copied = false;
        }, 2000);
    }
</script>
