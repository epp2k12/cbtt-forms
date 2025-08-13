<template>
  <div class="border border-gray-400 rounded-lg p-3 w-100 pt-5">

    <div v-if="formData" class="p-4 w-full ">
      <div class="mb-4 text-orange-500">
      Please review the details below before proceeding with the booking.
      </div>
      <table class="w-full mb-4">
        <thead>
          <tr>
            <th class="text-left bg-orange-500 text-white" colspan="2">Basic Information</th>
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
      <br>
      <table class="w-full mb-4">
        <thead>
          <tr>
            <th class="text-left bg-orange-500 text-white" colspan="2">Tour Information</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td class="w-[30%] text-sm">Tour Package</td>
            <td class="font-semibold text-sm">{{ formData.title || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm bg-orange-200 font-semibold" colspan="2">Tour Pricing</td>
          </tr>
          
          <tr>
            <td class="text-sm">Local</td>
            <td class="font-semibold text-sm">{{ price || 'Not provided' }}</td>            
          </tr>
          <tr>
            <td class="text-sm">Foreign</td>
            <td class="font-semibold text-sm">{{(Number(price) + 500).toFixed(2) || 'Not provided' }} (Add On : P500)</td>            
          </tr>

          <tr>
            <td class="text-sm bg-orange-200 font-semibold" colspan="2">Computation</td>
          </tr>
          <tr>
            <td class="text-sm bg-orange-100" colspan="2">Guests</td>
          </tr>
          <tr>
            <td class="text-sm">Local x {{ formData.local  }}</td>
            <td class="font-semibold text-sm">
              P {{ (Number(price) * noOfLocal).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
            </td>            
          </tr>
          <tr>
            <td class="text-sm">Foreign x {{ formData.foreign  }}</td>
            <td class="font-semibold text-sm">
                P {{ ((Number(price) + 500) * noOfForeign).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
            </td>  
          </tr>
          <tr>
            <td class="text-sm bg-orange-100" colspan="2">Add On</td>
          </tr>

          <tr v-if="formData.camera">
            <td class="text-sm">Camera Rental</td>
            <td class="font-semibold text-sm">800</td>            
          </tr>
          <tr v-else>
            <td class="text-xs text-orange-500" colspan="2"> - No Add On - </td>
          </tr>  

          <tr>
            <td class="font-semibold text-sm bg-orange-200">Total Price</td>
            <td class="font-semibold text-sm bg-orange-200">
              P {{ (Number(total)).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) }}
            </td>     
          </tr>

          <!-- 
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
          -->

        </tbody>
      </table>
      <!-- <p><strong>Phone:</strong> {{ formData.phone || 'Not provided' }}</p> -->
      <div class="flex justify-between items-center">
        <div>
          <button
            @click="goBack"
            class="px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-700"
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
const payload = ref(null);

const message = ref('');
const messageType = ref('');
const price = ref(5000);
const discount = ref(20);
const child_discount = ref(20);
const senior_discount = ref(20);
const pwd_discount = ref(20);
const total = ref(0);
const camera_price = ref(0);
const noOfLocal = ref(0);
const noOfForeign = ref(0);

onMounted( async() =>{

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';
  const nonce = window.wpApiSettings?.nonce || '';

  try {

    if (route.query.data) {
      formData.value = JSON.parse(route.query.data);
      message.value = 'Form data received successfully!';
      messageType.value = 'success';

      noOfLocal.value = formData.value.local || 0;
      noOfForeign.value = formData.value.foreign || 0;

    } else {

      message.value = 'No form data found in query parameters.';
      messageType.value = 'error';

    }

  } catch (error) {
    console.error('Error parsing form data:', error);
    message.value = 'Invalid form data. Please try submitting again.';
    messageType.value = 'error';
  }

  // console.log('Form data inside ValidateForm:', formData.value.id);

  const response_id = await fetch(restUrl + `wp/v2/posts/${formData.value.id}`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  });
  const data = await response_id.json();

  // console.log('Response data:', data.meta._custom_price);

  /* Assigning values to the reactive variables for tour package details */
  price.value = data.meta._custom_price;
  discount.value = data.meta._custom_discount;
  child_discount.value = data.meta._custom_children;
  senior_discount.value = data.meta._custom_senior;
  pwd_discount.value = data.meta._custom_pwd_discount;
  camera_price.value = formData.value.camera ? 800 : 0;
  total.value = (Number(price.value) * noOfLocal.value) + ((Number(price.value) + 500) * noOfForeign.value) + camera_price.value


  payload.value = {

    id: formData.value.id, // Post ID
    
    title: formData.value.title, // tour name
    name: formData.value.name, // client name
    tour_date: formData.value.tour_date, // tour date
    email: formData.value.email, // client email
    contact: formData.value.contact, // client contact number
    package_price: data.meta._custom_price, // tour package price

    local: formData.value.local, // no of local guests
    foreign: formData.value.foreign, // no of foreign guests
    pickup: formData.value.pickup, // pick-up location
    message: formData.value.message, // message requests

    local_price: Number(price.value),
    foreign_price: Number(price.value) + 500,

    total_local_price: (Number(price.value) * noOfLocal.value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),
    total_foreign_price: ((Number(price.value) + 500) * noOfForeign.value).toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 }),

    camera: formData.value.camera,
    camera_price: data.meta._custom_camera,
    scuba_diving_price: data.meta._custom_scuba_diving,
    lunch: data.meta._custom_lunch,

    sub_total: total.value,

  };

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

  console.log("Payload data: ", payload.value);

  try {
    const response = await fetch(restUrl + 'cbtt/v1/submit-form', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.cbttApp.nonce
      },
      // body: JSON.stringify(formData.value)
      body: JSON.stringify(payload.value)
    });

    const data = await response.json();
    // console.log('Response data:', data);
    router.push({
      name: 'thank-you',
      // query: { data: JSON.stringify(formData.value) }
      query: { data: JSON.stringify(payload.value) }
    });


  } catch (error) {
    console.error('Error submitting form:', error);
  }

};

</script>

<style scoped>
/* Add any additional styles as needed */
td {
  padding: 3px;
}
</style>