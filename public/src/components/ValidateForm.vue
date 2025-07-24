<template>
  <div class="border border-gray-400 rounded-lg p-3 w-100 pt-5">
    <h2 class="text-2xl font-bold mb-4">Form Submission Details</h2>
    <div v-if="message" class="mb-4" :class="messageType === 'success' ? 'text-green-600' : 'text-red-600'">
      {{ message }}
    </div>
    <div v-if="formData" class="p-4 w-full ">
      <table class="w-full mb-4">
        <thead>
          <tr>
            <th class="text-left" colspan="2">Basic Information</th>
          </tr>
        </thead>
        <tbody>
          <!-- <tr v-for="(value, key) in formData" :key="key">
            <td class="font-semibold">{{ key }}</td>
            <td>{{ value || 'Not provided' }}</td>
          </tr> -->
          <tr>
            <td class="w-[30%] text-sm">Full Name</td>
            <td class="font-semibold text-sm">{{ formData.name || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Email Address</td>
            <td class="font-semibold text-sm">{{ formData.email || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Contact Number</td>
            <td class="font-semibold text-sm">{{ formData.contact || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Tour Date</td>
            <td class="font-semibold text-sm">{{ formData.tour_date || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Pick-up Location</td>
            <td class="font-semibold text-sm">{{ formData.pickup || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Local Guests</td>
            <td class="font-semibold text-sm">{{ formData.local || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Foreign Guests</td>
            <td class="font-semibold text-sm">{{ formData.foreign || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Requests/Notes</td>
            <td class="font-semibold text-sm">{{ formData.message || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Camera Rental</td>
            <td class="font-semibold text-sm">{{ formData.camera || 'Not provided' }}</td>            
          </tr>
        </tbody>
      </table>

      <br><br>
      <table class="w-full mb-4">
        <thead>
          <tr>
            <th class="text-left" colspan="2">Tour Information</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="w-[30%] text-sm">Tour Title</td>
            <td class="font-semibold text-sm">{{ formData.title || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Tour Price</td>
            <td class="font-semibold text-sm">{{ price || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Discount</td>
            <td class="font-semibold text-sm">{{ discount || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Discount (children)</td>
            <td class="font-semibold text-sm">{{ child_discount || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Discount (senior)</td>
            <td class="font-semibold text-sm">{{ senior_discount || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Discount (PWD)</td>
            <td class="font-semibold text-sm">{{ pwd_discount || 'Not provided' }}</td>            
          </tr>
        </tbody>
      </table>
      <!-- <p><strong>Phone:</strong> {{ formData.phone || 'Not provided' }}</p> -->
      <div class="flex justify-between items-center">
        <div>
          <button
            @click="goBack"
            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-60"
          >
            Edit Details
          </button>
        </div>
        <div>
          <button 
              @click="submitForm2" 
              class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-60"
              >
              Book Tour
            </button>        
        </div>
      </div>
    </div>
    <div v-else class="text-red-600">
      No form data received.
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
const price = ref(5000);
const discount = ref(20);
const child_discount = ref(20);
const senior_discount = ref(20);
const pwd_discount = ref(20);

onMounted( async() =>{

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';
  const nonce = window.wpApiSettings?.nonce || '';

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

  console.log('Form data inside ValidateForm:', formData.value.id);


  const response_id = await fetch(restUrl + `wp/v2/posts/${formData.value.id}`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  });
  const data = await response_id.json();
  console.log('Response data:', data.meta._custom_price);
  price.value = data.meta._custom_price;
  discount.value = data.meta._custom_discount;
  child_discount.value = data.meta._custom_children;
  senior_discount.value = data.meta._custom_senior;
  pwd_discount.value = data.meta._custom_pwd_discount;


});

// const goBack = () => router.push({ name: 'contact', state: { formData: formData.value } });
const goBack = () => router.push({
  name: 'contact',
  query: { data: JSON.stringify(formData.value) }
});


const submitForm2 = async () => {

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';
  const nonce = window.wpApiSettings?.nonce || '';

  console.log("Form data: ", formData.value);

  try {
    const response = await fetch(restUrl + 'cbtt/v1/submit-form', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.cbttApp.nonce
      },
      body: JSON.stringify(formData.value)
    });

    const data = await response.json();
    console.log('Response data:', data);
  } catch (error) {
    console.error('Error submitting form:', error);
  }

};

</script>

<style scoped>
/* Add any additional styles as needed */
</style>