<!-- resources/views/livewire/create-user-fields.blade.php -->
<div class="w-full">
    <div class="col-span-12 mb-4">
        <x-display-validation-errors />
        <p class="text-secondary text-center lg:text-left my-2">
            {{__('All fields marked * are required')}}
        </p>
    </div>

    <div x-data="showImage()" class="mb-6 text-center">
        <img id="profile-picture" src="{{asset('application-images/user-profile-image.png')}}" alt="Profile Picture" class="w-32 h-32 rounded-full profile-image mx-auto block border border-black dark:border-white shadow" >
        <x-input type="file" id="profile-image-selector" name="profile_photo" class="hidden" label="Select Profile image" label-class="border p-2 bg-blue-700 hover:bg-blue-800 active:bg-blue-900 w-72 text-center m-auto rounded text-white mt-2" @change="showPreview(event)" accept="image/*" />
    </div>

    <div class="md:grid grid-cols-12 gap-4">
        <x-input name="first_name" id="first-name" label="First name *" placeholder="{{$role}}'s first name" group-class="col-span-12 md:col-span-4" />
        <x-input name="last_name" id="last-name" label="Last name *" placeholder="{{$role}}'s last name" group-class="col-span-12 md:col-span-4" />
        <x-input name="other_names" id="other-names" label="Other names *" placeholder="{{$role}}'s other names" group-class="col-span-12 md:col-span-4" />
    </div>

    <div class="md:grid grid-cols-12 gap-4 mt-4">
        <x-input type="date" id="birthday" name="birthday" placeholder="Choose {{$role}}'s birthday..." label="Birthday *" group-class="col-span-12 md:col-span-4 w-full"/>
        <x-input type="number" id="age" name="age" placeholder="{{$role}}'s age" label="Age" group-class="col-span-12 md:col-span-4" />
        <x-select id="gender" name="gender" label="Gender *" group-class="col-span-12 md:col-span-4" >
            @php ($genders = ['Male', 'Female'])
            @foreach ($genders as $gender)
                <option value="{{$gender}}" >{{$gender}}</option>
            @endforeach
        </x-select>
    </div>

    <div class="md:grid grid-cols-12 gap-4 mt-4">
        <x-input id="address" name="address" placeholder="{{$role}}'s address" group-class="col-span-12 md:col-span-8 no-resize" label="Address *" />
        <x-input id="phone" name="phone" label="Phone number" placeholder="{{$role}}'s phone number" group-class="col-span-12 md:col-span-4" />
    </div>

    <div class="md:grid grid-cols-12 gap-4 mt-4">
        <div class="col-span-12 md:col-span-8">
            <livewire:nationality-and-state-input-fields />
        </div>
        <x-input id="city" name="city" label="City *" placeholder="{{$role}}'s city" group-class="col-span-12 md:col-span-4"/>
    </div>

    <script>
        function showImage() {
            return {
                showPreview(event) {
                    if (event.target.files.length > 0) {
                        var src = URL.createObjectURL(event.target.files[0]);
                        var preview = document.getElementById("profile-picture");
                        preview.src = src;
                        preview.style.display = "block";
                    }
                }
            }
        }
    </script>
</div>