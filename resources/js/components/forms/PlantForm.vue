<template>
  <div class="plant-form">
  <Modal @close="$emit('close')">
    <template #header>
      <h1>Plant Form</h1>
    </template>
    <template #content>
      <Form @submit="submitForm">
        <template #inputs>
          <input type="text" v-model="newPlant.name" placeholder="Species" required minlength="2" maxlength="255"/>
          <input type="text" v-model="newPlant.variety" placeholder="Variety" required maxlength="255"/>
          <input type="text" v-model="newPlant.description" placeholder="Description" maxlength="255"/>
          <input type="text" v-model="newPlant.image" placeholder="Image" maxlength="255"/>
        </template>
        <template #buttons>
          <Button type="submit" :disabled="loading">Submit</Button>
        </template>
      </Form>
    </template>
  </Modal>
</div>
</template>

<script>
import { createPlant } from '../../utils/api'
export default {
  name: 'PlantForm',
  data() {
    return {
      newPlant: {
        name: '',
        description: '',
        variety: '',
        image: ''
      },
      loading: false
    }
  },
  methods: {
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      createPlant(this.newPlant).then(response => {
        console.log(response.data)
      }).finally(() => {
        this.loading = false
        this.$emit('close')
      })
    }
  },
  computed: {
    // Add your computed properties here
  },
  mounted() {
    // Add your mounted logic here
  }
}
</script>

<style scoped lang="scss">
</style>