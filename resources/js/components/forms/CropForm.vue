<template>
  <div class="crop-form">
    <h1>Crop Form</h1>
    <Form @submit="submitForm">
      <template #inputs>
        <!-- Add your form elements here -->
      </template>
      <template #buttons>
        <button type="submit">Submit</button>
      </template>
    </Form>
  </div>
</template>

<script>
import { get } from '../../utils/api'
export default {
  name: 'CropForm',
  data () {
    return {
      // Add your data properties here
    }
  },
  methods: {
    submitForm () {
      get('plants').then(response => {
        console.log(response.data)
      })
    }
  },
  computed: {
    // Add your computed properties here
  },
  mounted() {
  }
}
</script>

<style scoped lang="scss">
.crop-form {
  /* Add your styles here */
}
</style>