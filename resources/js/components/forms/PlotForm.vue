<template>
  <div class="plot-form">
  <Modal @close="$emit('close')">
    <template #header>
      <h2>Plot Form</h2>
    </template>
    <template #content>
      <Form @submit="submitForm">
        <template #inputs>
          <Input type="text" v-model="currPlot.name" label="Plot name" required minlength="2" maxlength="255"/>
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
import { createPlot, updatePlot } from '../../utils/api'
import { isEmpty } from '../../utils/helpers';
export default {
  name: 'PlotForm',
  props: {
    val: Object,
  },
  emits: ['add', 'patch', 'close'],
  data () {
    return {
      currPlot: isEmpty(this.val) || this.val ? this.val : { name: '' },
      loading: false
    }
  },
  methods: {
    submitForm (e) {
      this.loading = true
      e.preventDefault()
      if (this.currPlot.id) {
        updatePlot(this.currPlot.id, this.currPlot).then(response => {
          this.$emit('patch', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } else {
        createPlot(this.currPlot).then(response => {
          this.$emit('add', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      }
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
h2 {
  margin: 0;
}
</style>