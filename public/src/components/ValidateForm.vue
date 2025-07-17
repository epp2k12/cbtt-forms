<template>
  <div class=" border border-brown-500 rounded-lg p-10">
    <h2>Thanks!</h2>
    <p>You submitted: {{ formData }}</p>
  </div>
</template>

<script setup>
import { defineProps } from 'vue';

const props = defineProps({
  formData: {
    type: Object,
    required: true,
  },
});

console.log('Form data received in ValidateForm:', props.formData);
</script>