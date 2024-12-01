<template>
  <div class="plant-form">
  <Modal @close="$emit('close')">
    <template #header>
      <h2>Plant Form</h2>
    </template>
    <template #content>
      <Form @submit="submitForm">
        <template #inputs>
          <Input type="text" v-model="currPlant.name" label="Species" required minlength="2" maxlength="255"/>
          <Input type="text" v-model="currPlant.variety" label="Variety" required maxlength="255"/>
          <Input type="textarea" v-model="currPlant.description" label="Description" maxlength="255"/>
          <Input :modelValue="currPlant.image" type="file" label="Image" @change="e => currPlant.image = e.target.value"/>
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
import { createPlant, updatePlant } from '../../utils/api'
import { isEmpty } from '../../utils/helpers';
export default {
  name: 'PlantForm',
  props: {
    val: Object,
  },
  emits: ['add', 'patch', 'close'],
  data () {
    return {
      currPlant: isEmpty(this.val) || this.val ? this.val : {
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
      if (this.currPlant.id) {
        updatePlant(this.currPlant.id, this.currPlant).then(response => {
          this.$emit('patch', response.data.data)
        }).finally(() => {
          this.loading = false
          this.$emit('close')
        })
      } else {
        createPlant(this.currPlant).then(response => {
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