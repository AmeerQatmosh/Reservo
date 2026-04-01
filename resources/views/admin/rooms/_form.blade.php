<div class="space-y-5">
    <div>
        <label for="name" class="block text-sm font-medium text-gray-900">Name</label>
        <input id="name" name="name" type="text" value="{{ old('name', $room?->name) }}" class="app-field">
    </div>

    <div>
        <label for="capacity" class="block text-sm font-medium text-gray-900">Capacity</label>
        <input id="capacity" name="capacity" type="number" min="1" value="{{ old('capacity', $room?->capacity ?? 1) }}" class="app-field">
    </div>

    <div>
        <label for="description" class="block text-sm font-medium text-gray-900">Description</label>
        <textarea id="description" name="description" rows="6" class="app-field">{{ old('description', $room?->description) }}</textarea>
    </div>

    <div>
        <label for="location" class="block text-sm font-medium text-gray-900">Location (optional)</label>
        <input id="location" name="location" type="text" value="{{ old('location', $room?->location) }}" placeholder="e.g. North Tower · Floor 3" class="app-field">
    </div>

    <div>
        <label for="size_sqm" class="block text-sm font-medium text-gray-900">Size (m², optional)</label>
        <input id="size_sqm" name="size_sqm" type="number" min="1" max="50000" value="{{ old('size_sqm', $room?->size_sqm) }}" placeholder="e.g. 45" class="app-field">
    </div>

    <div>
        <label for="hourly_rate" class="block text-sm font-medium text-gray-900">Hourly rate (optional)</label>
        <input
            id="hourly_rate"
            name="hourly_rate"
            type="text"
            inputmode="decimal"
            value="{{ old('hourly_rate', $room?->hourly_rate) }}"
            placeholder="e.g. 45.00"
            class="app-field"
        >
        <p class="mt-1 text-xs text-gray-500">
            Shown as an estimate when people book. Reservo does not process payments—billing stays outside the app.
        </p>
    </div>

    <div>
        <label for="amenities_text" class="block text-sm font-medium text-gray-900">Amenities (optional)</label>
        <textarea id="amenities_text" name="amenities_text" rows="5" class="app-field" placeholder="One per line">{{ old('amenities_text', implode("\n", $room?->amenities ?? [])) }}</textarea>
        <p class="mt-1 text-xs text-gray-500">List equipment and perks—one item per line (max 30 lines).</p>
    </div>

    <div>
        <label for="image_url" class="block text-sm font-medium text-gray-900">Photo URL (optional)</label>
        <input id="image_url" name="image_url" type="text" inputmode="url" value="{{ old('image_url', $room?->image_url) }}" placeholder="https://…" class="app-field">
        <p class="mt-1 text-xs text-gray-500">Paste a direct image link (e.g. from your CDN or Unsplash).</p>
    </div>

    <button type="submit" class="w-full rounded-2xl bg-gray-900 px-4 py-3 text-sm font-medium text-white shadow-sm transition hover:bg-gray-800 sm:w-auto">
        {{ $submitLabel }}
    </button>
</div>
