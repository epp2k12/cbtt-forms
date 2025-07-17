<template>
  <div class="cbtt-form-form border border-brown-500 rounded-lg p-10">
    <form @submit.prevent="submitForm">

      <div class="form-group">
        <label>Tour Title : </label>
        <input v-model="form.title" type="text" readonly>
      </div>

      <div class="form-group">
        <label>Name*</label>
        <input v-model="form.name" type="text" required>
      </div>
      
      <div class="form-group">
        <label>Email*</label>
        <input v-model="form.email" type="email" required>
      </div>
      
      <div class="form-group">
        <label>Contact Num</label>
        <input v-model="form.phone" type="tel">
      </div>
      
      <div class="form-group">
        <label>Tour Date</label>
        <input v-model="form.tour_date" type="date">
      </div>
      
      <div class="form-group">
        <label>Message</label>
        <textarea v-model="form.message" rows="4"></textarea>
      </div>
      
      <div v-if="message" :class="['response', messageType]">
        {{ message }}
      </div>
      
      <button type="submit" :disabled="loading">
        {{ loading ? 'Sending...' : 'Submit Request' }}
      </button>


    </form>

    <button 
      :disabled="loading" 
      @click="submitForm2" 
      class="mt-4 border border-blue-300 bg-blue-300 p-5"
      >
      test
    </button>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router'

const emit = defineEmits(['formSubmitted']);

const props = defineProps({
  postTitle: {
    type: String,
    required: true,
  },
});

console.log('Post title from props:', props.postTitle);

const router = useRouter()

const form = ref({
  title: props.postTitle,
  name: '',
  email: '',
  phone: '',
  tour_date: '',
  message: ''
});


const loading = ref(false);
const message = ref('');
const messageType = ref('');

const submitForm1 = async () => {
  loading.value = true;
  message.value = '';
  
  try {
    const response = await window.jQuery.post(ajaxurl, {
      action: 'tour_form_submit',
      security: tourFormAjax.nonce,
      ...form.value
    });

    if (response.success) {
      message.value = response.data.message;
      messageType.value = 'success';
      resetForm();
    } else {
      message.value = response.data.message;
      messageType.value = 'error';
    }
  } catch (error) {
    message.value = 'An error occurred. Please try again.';
    messageType.value = 'error';
  } finally {
    loading.value = false;
  }
};

const submitForm = async () => {
    console.log("Form data: ", form.value);
    // router.push({
    //     name: 'validate-form',
    //     query: { data: JSON.stringify(form.value) }
    // });
    emit ('formSubmitted', form.value);
}

const resetForm = () => {
  form.value = {
    name: '',
    email: '',
    phone: '',
    tour_date: '',
    message: ''
  };
};


onMounted(async () => {

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';

  const nonce = window.wpApiSettings?.nonce || '';

  console.log('Site URL:', siteUrl);
  console.log('REST URL:', restUrl);

  // console.log('This is the mounted hook in contact form!!!');
  // try {
    const response = await fetch(restUrl + 'cbtt/v1/get-posts', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json',
        // If you need authentication, add nonce or auth headers here
      },
      // body: JSON.stringify(form.value)
    });

    const data = await response.json();
    console.log('Response data:', data);

  //   if (data.success) {

  //     console.log('Data received:', data);

  //     // message.value = data.message;
  //     // messageType.value = 'success';
  //     // resetForm();
  //   } else {
  //     // message.value = data.message || 'Submission failed.';
  //     // messageType.value = 'error';
  //   }
  // } catch (error) {
  //   // message.value = 'An error occurred. Please try again.';
  //   // messageType.value = 'error';
  // } finally {
  //   // loading.value = false;
  // }  


});

const submitForm2 = async () => {

  const siteUrl = window.cbttApp?.siteUrl || '';
  const restUrl = window.cbttApp?.restUrl || '';
  const nonce = window.wpApiSettings?.nonce || '';

  console.log("Form data: ", form.value);
  const payload = ref({
    name: form.value.name,
    email: form.value.email,
    phone: form.value.phone,
    title: form.value.title,
    title: form.value.tour_date,
  })

  try {
    const response = await fetch(restUrl + 'cbtt/v1/submit-form', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-WP-Nonce': window.cbttApp.nonce
      },
      body: JSON.stringify(payload.value)
    });

    const data = await response.json();
    console.log('Response data:', data);
  } catch (error) {
    console.error('Error submitting form:', error);
  }


};

</script>
