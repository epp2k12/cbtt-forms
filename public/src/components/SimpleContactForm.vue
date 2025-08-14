<template>
  <div class="border border-gray-400 rounded-lg p-3 w-100 pt-5">
    <div class="mb-10 text-lg text-red-500">
      Please enter your details below to book your tour.
    </div>

    <form @submit.prevent="submitForm">
      <div class="relative w-full mb-4">
        <input
          type="text"
          id="tour-title"
          v-model="form.title"
          placeholder=" "
          required
          readonly
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-xs"
        />
        <label
          for="tour-title"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Tour Package
        </label>
      </div>

      <div class="relative w-full mb-4">
        <input
          type="text"
          id="name"
          v-model="form.name"
          placeholder=" "
          required
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
        />
        <label
          for="name"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Full Name
        </label>
      </div>

      <div class="relative w-full mb-4">
        <input
          type="text"
          id="contact"
          v-model="form.contact"
          placeholder=" "
          required
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
        />
        <label
          for="contact"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Contact Number
        </label>
      </div>

      <div class="relative w-full mb-4">
        <input
          type="email"
          id="email"
          v-model="form.email"
          placeholder=" "
          required
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
        />
        <label
          for="email"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Email Address
        </label>
      </div>

      <div class="relative w-full mb-4">
        <input
          type="date"
          id="tourdate"
          v-model="form.tour_date"
          placeholder=" "
          required
          :min="today"
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
        />
        <label
          for="tourdate"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Tour Date
        </label>
      </div>

      <div class="relative w-full mb-4">
        <input
          type="number"
          id="local"
          v-model="form.local"
          placeholder=" "
          required
          min="0"
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
        />
        <label
          for="local"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Number of Local Guests
        </label>
      </div>

      <div class="relative w-full mb-4">
        <input
          type="number"
          id="foreign"
          v-model="form.foreign"
          placeholder=" "
          required
          min="0"
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
        />
        <label
          for="foreign"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Number of Foreign Guests
        </label>
      </div>

      <div class="relative w-full mb-4">
        <input
          type="text"
          id="pickup"
          v-model="form.pickup"
          placeholder=" "
          required
          class="peer h-12 w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 pl-3 text-sm"
        />
        <label
          for="pickup"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Pick-up Location (Hotel/Address)
        </label>
      </div>

      <div class="flex items-center space-x-2 mb-4">
        <input
          id="camera"
          name="camera"
          v-model="form.camera"
          type="checkbox"
          class="h-4 w-4 text-green-600 border-gray-300 rounded focus:ring-green-500"
        />
        <label for="camera" class="text-sm text-gray-700">
          Camera Rental
        </label>
      </div>

      <div class="relative w-full mb-4">
        <textarea
          id="message"
          placeholder=" "
          v-model="form.message"
          required
          rows="4"
          class="peer w-full border border-gray-300 rounded-lg text-gray-900 placeholder-transparent focus:outline-none focus:border-green-500 p-3 resize-none text-sm"
        ></textarea>
        <label
          for="message"
          class="
            absolute left-[10px] -top-2 text-orange-500 text-xs transition-all
            peer-placeholder-shown:text-gray-400 peer-placeholder-shown:top-3 peer-placeholder-shown:text-sm
            peer-focus:-top-2 peer-focus:text-orange-500 peer-focus:text-xs bg-white pl-1 pr-1
          "
        >
          Additional Tour Notes/Requests
        </label>
      </div>

      <!-- Display validation error messages -->
      <div v-if="errors.length" class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg text-sm">
        <ul>
          <li v-for="error in errors" :key="error">{{ error }}</li>
        </ul>
      </div>

      <div class="text-right">
        <button
          type="submit"
          class="px-5 py-2 bg-[#0C91EB] text-white rounded-lg shadow hover:bg-[#0a7fcb] focus:outline-none focus:ring-2 focus:ring-[#0C91EB] focus:ring-offset-2 transition text-sm"
          :disabled="loading"
        >
          {{ loading ? 'Submitting...' : 'Submit' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';

const emit = defineEmits(['formSubmitted']);
const route = useRoute();
const router = useRouter();
const postTitle = ref('Default Title');
const postID = ref(0);
const today = ref('');
const errors = ref([]); // Store validation errors
const loading = ref(false);
const message = ref('');
const messageType = ref('');

const form = ref({
  title: postTitle.value,
  id: postID.value,
  name: '',
  contact: '',
  email: '',
  tour_date: '',
  pickup: '',
  local: 0,
  foreign: 0,
  camera: false,
  message: '',
  price: '5000', // Default from previous setup
  discount: '5',
  children_discount: '5',
  senior_discount: '20',
  pwd_discount: '20',
});

const validateForm = () => {
  errors.value = []; // Reset errors

  // Validate local and foreign guests (at least one must be > 0)
  if (Number(form.value.local) === 0 && Number(form.value.foreign) === 0) {
    errors.value.push('At least one local or foreign guest must be specified.');
  }

  // Validate email format
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(form.value.email)) {
    errors.value.push('Please enter a valid email address.');
  }

  // Validate phone number (7-15 digits, allowing dashes, spaces, or parentheses)
  // const phoneRegex = /^\+?[\d\s()-]{7,15}$/;
  // if (!phoneRegex.test(form.value.contact)) {
  //   errors.value.push('Please enter a valid phone number (7-15 digits, may include +, -, (), or spaces).');
  // }

  return errors.value.length === 0; // Return true if no errors
};

const submitForm = async () => {
  if (!validateForm()) {
    return; // Stop submission if validation fails
  }

  loading.value = true;

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';
  const nonce = window.wpApiSettings?.nonce || '';


  try {
    const response = await fetch(restUrl + 'cbtt/v1/submit-simple-form', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.cbttApp.nonce
      },
      // body: JSON.stringify(formData.value)
      body: JSON.stringify(form.value)
    });

      try {
        router.push({
          name: 'thank-you',
          query: { 
            data: JSON.stringify(form.value),
            form_type: 'simple' // Hardcoded as 'simple'
          },
        });
        // emit('formSubmitted', form.value);
      } catch (error) {
        errors.value.push('An error occurred while submitting the form. Please try again.');
      } finally {
        loading.value = false;
      }

  } catch (error) {
    console.error('Error submitting form:', error);
  }



};

const resetForm = () => {
  form.value = {
    title: postTitle.value,
    id: postID.value,
    name: '',
    contact: '',
    email: '',
    tour_date: '',
    pickup: '',
    local: 0,
    foreign: 0,
    camera: false,
    message: '',
    price: '5000',
    discount: '5',
    children_discount: '5',
    senior_discount: '20',
    pwd_discount: '20',
  };
};

onMounted(async () => {
  // Set today's date for tour_date min attribute
  const date = new Date();
  today.value = date.toISOString().split('T')[0]; // Format as YYYY-MM-DD

  // Restore form data from query if present
  if (route.query.data) {
    try {
      const data = JSON.parse(route.query.data);
      form.value = { ...form.value, ...data };
      console.log('Restored form data:', form.value);
    } catch (e) {
      console.error('Failed to parse form data', e);
    }
  }

  // Get site and REST URLs
  const restUrl = window.cbttApp?.restUrl || '';

  // Access post title and ID from data attributes
  const el = document.getElementById('cbtt-simple-contact-form');
  postTitle.value = el?.dataset.postTitle || 'Default Title';
  postID.value = el?.dataset.postId;

  form.value.title = postTitle.value;
  form.value.id = postID.value;

  // Fetch custom fields from REST API
  try {
    const response = await fetch(restUrl + `wp/v2/posts/${postID.value}`, {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
      },
    });

    if (!response.ok) {
      throw new Error(`HTTP error! status: ${response.status}`);
    }

    const data = await response.json();
    // Update form with custom field values or keep defaults
    form.value.price = data.meta?._custom_price || form.value.price;
    form.value.discount = data.meta?._custom_discount || form.value.discount;
    form.value.children_discount = data.meta?._custom_children || form.value.children_discount;
    form.value.senior_discount = data.meta?._custom_senior || form.value.senior_discount;
    form.value.pwd_discount = data.meta?._custom_pwd_discount || form.value.pwd_discount;

    // console.log('Fetched custom fields:', {
    //   price: form.value.price,
    //   discount: form.value.discount,
    //   children_discount: form.value.children_discount,
    //   senior_discount: form.value.senior_discount,
    //   pwd_discount: form.value.pwd_discount,
    // });
  } catch (error) {
    console.error('Error fetching custom fields:', error);
  }

});
</script>

<style scoped>
input[type="text"],
input[type="email"],
input[type="number"],
input[type="date"] {
  height: 50px;
  padding: 10px;
  box-sizing: border-box;
  font-size: 13px;
}
</style>