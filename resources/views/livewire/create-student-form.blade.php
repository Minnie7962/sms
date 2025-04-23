<div class="card">
    <div class="card-header">
        <h2 class="card-title">Create Student</h2>
    </div>
    <div class="card-body">
        <form action="{{route('students.store')}}" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="mb-4">
                <!-- Student Basic Info Section -->
                <h3 class="text-lg font-medium mb-2">Basic Information</h3>
                <div class="bg-gray-50 p-4 rounded mb-4">
                    <livewire:create-user-fields role="Student" />
                </div>
            </div>
            
            @csrf
            
            <div class="mb-4">
                <!-- Student Record Fields Section -->
                <div class="bg-gray-50 p-4 rounded mb-4">
                    <livewire:create-student-record-fields />
                </div>
            </div>
            
            <div class="flex justify-center md:justify-start">
                <x-button label="Create" theme="primary" icon="fas fa-key" type="submit" class="w-full md:w-3/12"/>
            </div>
        </form>
    </div>
</div>