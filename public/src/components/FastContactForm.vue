<template>
  <div class="p-6">
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

      <!-- 1️⃣ Available Options -->
      <div class="border p-4 rounded-lg">
        <h2 class="font-bold mb-2">Available Options</h2>
        <draggable
          v-model="availableOptions"
          group="options"
          item-key="name"
          class="min-h-[150px] border rounded p-2 grid grid-cols-2 gap-2"
        >
          <template #item="{ element }">
            <div class="p-2 bg-gray-100 rounded cursor-move text-center">
              {{ element.name }}
            </div>
          </template>
        </draggable>
      </div>

      <!-- 2️⃣ Selected Options -->
      <div class="border p-4 rounded-lg">
        <h2 class="font-bold mb-2">Selected Options</h2>
        <draggable
          v-model="selectedOptions"
          group="options"
          item-key="name"
          class="min-h-[150px] border rounded p-2"
        >
          <template #item="{ element }">
            <div class="p-2 bg-green-100 rounded mb-2 cursor-move">
              {{ element.name }}
            </div>
          </template>
        </draggable>

        <!-- Payload Preview -->
        <button
          class="mt-4 bg-[#0C91EB] text-white px-4 py-2 rounded"
          @click="submit"
        >
          Submit Payload
        </button>
        <!-- <pre class="mt-2 bg-gray-50 p-2 rounded">{{ payload }}</pre> -->

        
      </div>

      <!-- 3️⃣ Customer Details Form -->
      <div class="border p-4 rounded-lg">
        <h2 class="font-bold mb-4">Customer Details</h2>
        <form @submit.prevent="submitBooking" class="space-y-3">

          <input v-model="form.fullname" type="text" placeholder="Fullname" class="input" />
          <input v-model="form.contact" type="text" placeholder="Contact" class="input" />
          <input v-model="form.email" type="email" placeholder="Email" class="input" />
          <input v-model="form.tourDate" type="date" class="input" />

          <input v-model.number="form.days" type="number" placeholder="No of Days" class="input" />
          <input v-model.number="form.localGuests" type="number" placeholder="No of Local Guest" class="input" />
          <input v-model.number="form.foreignGuests" type="number" placeholder="No of Foreign Guest" class="input" />

          <input v-model="form.pickupLocation" type="text" placeholder="Pick-up Location" class="input" />

          <label class="flex items-center space-x-2 text-md">
            <input type="checkbox" v-model="form.accomodation" />
            <span>Include Price for Accommodation</span>
          </label>

          <button type="submit" class="w-full bg-[#0C91EB] text-white py-2 rounded-lg hover:bg-blue-600">
            Book Now
          </button>
        </form>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import draggable from 'vuedraggable'

// 1️⃣ Options (10 items)
const availableOptions = ref([
  { name: 'Option 1' },
  { name: 'Option 2' },
  { name: 'Option 3' },
  { name: 'Option 4' },
  { name: 'Option 5' },
  { name: 'Option 6' },
  { name: 'Option 7' },
  { name: 'Option 8' },
  { name: 'Option 9' },
  { name: 'Option 10' }
])

const selectedOptions = ref([])

// 2️⃣ Payload for selected options
const payload = computed(() => selectedOptions.value.map(o => o.name))

const submit = () => {
  alert('Payload: ' + JSON.stringify(payload.value))
}

// 3️⃣ Customer form
const form = ref({
  fullname: '',
  contact: '',
  email: '',
  tourDate: '',
  days: 1,
  localGuests: 0,
  foreignGuests: 0,
  pickupLocation: '',
  accomodation: false
})

// 4️⃣ Submit Booking
const submitBooking = () => {
  const bookingPayload = {
    customer: form.value,
    selectedOptions: payload.value
  }
  alert('Booking Payload:\n' + JSON.stringify(bookingPayload, null, 2))
}
</script>

<style scoped>
.input {
  @apply w-full p-2 border rounded-lg;
}
.cursor-move {
  cursor: grab;
}
</style>
