<div>
    <div x-data="{
        isTracking: @entangle('isTracking'),
        elapsedTime: @entangle('elapsedTime'),
        task: @entangle('data'),
        validationStr: @entangle('validationStr'),
        validationError: '', 
        timer: null,
        startTimer() {
            if (this.task['tasks'].length < 1) {
                this.validationError = this.validationStr;
                return;
            }
            $wire.startTracking();
            this.validationError = '';
            if (this.isTracking) return;
            this.isTracking = true;
            this.timer = setInterval(() => {
                $wire.updateElapsedTime();
            }, 1000);
        },
        stopTimer() {
            if (!this.isTracking) return;
            this.isTracking = false;
            clearInterval(this.timer);
        },
        init() {
            if (this.isTracking) {
                this.timer = setInterval(() => {
                    $wire.updateElapsedTime();
                }, 1000);
            }
        }
    }" class="w-full p-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="text-center">
            <h1 class="mb-4 text-2xl font-bold dark:text-white">Time Tracking</h1>
            <div class="mb-4 font-mono text-4xl text-gray-400 dark:text-white" x-text="elapsedTime"></div>
            <div class="flex justify-center mb-4 space-x-4">
                <button @click="startTimer();" :disabled="isTracking"
                    class="px-4 py-2 text-white bg-green-500 rounded-md hover:bg-green-600 disabled:bg-green-300">Start</button>
                <button @click="$wire.stopTracking(); stopTimer;" :disabled="!isTracking"
                    class="px-4 py-2 text-white bg-red-500 rounded-md hover:bg-red-600 disabled:bg-red-300">Stop</button>
            </div>
            <div class="mb-4">
                <h1 class="font-bold text-red-400" x-text='validationError'></h1>
                {{ $this->form }}
            </div>
        </div>
        @script
            <script>
                function startTimer() {
                    alert(1);
                    if (this.isTracking) return;
                    this.isTracking = true;
                    this.timer = setInterval(() => {
                        alert(this.isTracking);
                        $wire.dispatch('updateElapsedTime');
                    }, 1000);
                }


                function stopTimer() {
                    alert(1);
                    if (!this.isTracking) return;
                    this.isTracking = false;
                    clearInterval(this.timer);
                }

                document.addEventListener('livewire:load', function() {
                    @this.on('startTracking', () => {
                        startTimer();
                    });
                });

                window.addEventListener('beforeunload', function(e) {
                    if (@this.isTracking) {
                        navigator.sendBeacon('/api/save-tracking-state', JSON.stringify({
                            timesheet_id: @this.timesheetId,
                            start_time: @this.startTime,
                            is_active: @this.isTracking
                        }));
                    }
                });
            </script>
        @endscript

    </div>
</div>
