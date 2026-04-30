<div class="space-y-6">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-900">{{ __('Name') }}</label>
        <input id="name" name="name" type="text" value="{{ old('name', $room?->name) }}" class="app-field" autocomplete="off">
    </div>

    <div class="grid gap-5 sm:grid-cols-3">
        <div>
            <label for="capacity" class="block text-sm font-medium text-gray-900">{{ __('Capacity') }}</label>
            <input id="capacity" name="capacity" type="number" min="1" value="{{ old('capacity', $room?->capacity ?? 1) }}" class="app-field">
        </div>
        <div>
            <label for="size_sqm" class="block text-sm font-medium text-gray-900">{{ __('Size (m², optional)') }}</label>
            <input id="size_sqm" name="size_sqm" type="number" min="1" max="50000" value="{{ old('size_sqm', $room?->size_sqm) }}" placeholder="{{ __('e.g. 45') }}" class="app-field">
        </div>
        <div>
            <label for="hourly_rate" class="block text-sm font-medium text-gray-900">{{ __('Hourly rate (optional)') }}</label>
            <input
                id="hourly_rate"
                name="hourly_rate"
                type="text"
                inputmode="decimal"
                value="{{ old('hourly_rate', $room?->hourly_rate) }}"
                placeholder="{{ __('e.g. 45.00') }}"
                class="app-field"
            >
        </div>
    </div>
    <p class="mt-1 text-xs text-gray-500">
        {{ __('Shown as an estimate when people book. Reservo does not process payments—billing stays outside the app.') }}
    </p>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-900">{{ __('Description') }}</label>
        <textarea id="description" name="description" rows="5" class="app-field">{{ old('description', $room?->description) }}</textarea>
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-gray-900">{{ __('Location (optional)') }}</label>
        <input id="location" name="location" type="text" value="{{ old('location', $room?->location) }}" placeholder="{{ __('e.g. Main campus · North tower · Floor 3') }}" class="app-field" autocomplete="off">
    </div>

    <div>
        <label for="amenities_text" class="block text-sm font-medium text-gray-900">{{ __('Amenities (optional)') }}</label>
        <textarea id="amenities_text" name="amenities_text" rows="3" class="app-field" placeholder="{{ __('e.g. Whiteboard, HDMI, Video bar') }}">{{ old('amenities_text', implode(', ', $room?->amenities ?? [])) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">{{ __('Separate items with commas (up to 30). Line breaks also work if you paste a list.') }}</p>
    </div>

    <div>
        <label for="image_url" class="block text-sm font-medium text-gray-900">{{ __('Photo URL (optional)') }}</label>
        <input id="image_url" name="image_url" type="text" inputmode="url" value="{{ old('image_url', $room?->image_url) }}" placeholder="https://…" class="app-field" autocomplete="off">
        <p class="mt-1 text-xs text-gray-500">{{ __('Paste a direct image link (e.g. from your CDN or Unsplash).') }}</p>
    </div>

    <div class="flex flex-wrap items-center gap-3">
        <a
            href="{{ $cancelUrl }}"
            class="inline-flex w-full items-center justify-center rounded-2xl border border-gray-200 bg-white px-4 py-3 text-sm font-medium text-gray-800 shadow-sm transition hover:bg-gray-50 sm:w-auto"
        >{{ __('Cancel') }}</a>
        <button type="submit" class="inline-flex w-full items-center justify-center rounded-2xl bg-teal-600 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-teal-700 sm:w-auto">
            {{ $submitLabel }}
        </button>
    </div>
</div>
