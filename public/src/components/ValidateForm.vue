<template>
  <div class="p-6 max-w-lg mx-auto">
    <h2 class="text-2xl font-bold mb-4">Form Submission Details</h2>
    <div v-if="message" class="mb-4" :class="messageType === 'success' ? 'text-green-600' : 'text-red-600'">
      {{ message }}
    </div>
    <div v-if="formData" class="border border-gray-300 rounded-lg p-4">
      <p><strong>Tour Title:</strong> {{ formData.title }}</p>
      <p><strong>Full Name:</strong> {{ formData.name }}</p>
      <p><strong>Contact Number:</strong> {{ formData.contact }}</p>
      <p><strong>Email Address:</strong> {{ formData.email }}</p>
      <p><strong>Phone:</strong> {{ formData.phone || 'Not provided' }}</p>
      <p><strong>Tour Date:</strong> {{ formData.tour_date }}</p>
      <p><strong>Pick-up Location:</strong> {{ formData.pickup }}</p>
      <p><strong>Number of Local Guests:</strong> {{ formData.local }}</p>
      <p><strong>Number of Foreign Guests:</strong> {{ formData.foreign }}</p>
      <p><strong>Camera Rental:</strong> {{ formData.camera ? 'Yes' : 'No' }}</p>
      <p><strong>Additional Notes:</strong> {{ formData.message || 'None' }}</p>
    </div>
    <div v-else class="text-red-600">
      No form data received.
    </div>
    <div class="mt-4">
      <button
        @click="goBack"
        class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600"
      >
        Back to Form
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const route = useRoute();
const router = useRouter();
const formData = ref(null);
const message = ref('');
const messageType = ref('');

onMounted(() => {
  try {
    if (route.query.data) {
      formData.value = JSON.parse(route.query.data);
      console.log('Received form data:', formData.value);
      message.value = 'Form data received successfully!';
      messageType.value = 'success';
    } else {
      message.value = 'No form data found in query parameters.';
      messageType.value = 'error';
    }
  } catch (error) {
    console.error('Error parsing form data:', error);
    message.value = 'Invalid form data. Please try submitting again.';
    messageType.value = 'error';
  }
});

// const goBack = () => router.push({ name: 'contact', state: { formData: formData.value } });
const goBack = () => router.push({
  name: 'contact',
  query: { data: JSON.stringify(formData.value) }
});

</script>

<style scoped>
/* Add any additional styles as needed */
</style>