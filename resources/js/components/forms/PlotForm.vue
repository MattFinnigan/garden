<template>
  <div class="Plot-form">
  <Modal @close="$emit('close')">
    <template #header>
      <h1>Plot Form</h1>
    </template>
    <template #content>
      <Form @submit="submitForm">
        <template #inputs>
          <input type="text" v-model="newPlot.name" placeholder="Plot name" required minlength="2" maxlength="255"/>
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
import { createPlot } from '../../utils/api'
export default {
  name: 'PlotForm',
  data() {
    return {
      newPlot: {
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
      createPlot(this.newPlot).then(response => {
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