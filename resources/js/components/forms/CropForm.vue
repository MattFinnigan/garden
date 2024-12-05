<template>
  <div class="crop-form">
    <CropEntryForm
      v-if="val"
      title="Add a Crop"
      :crop="crop"
      :val="val"
      :plants="plants"
      :locations="locations"
      :handleSubmit="false"
      @submit="submitForm"
      @close="$emit('close')"/>
  </div>
</template>

<script>
import { createCrop } from '../../utils/api'
import CropEntryForm from './CropEntryForm.vue';

export default {
  components: {
    CropEntryForm
  },
  name: 'CropForm',
  props: {
    plants: Array,
    locations: Array,
    val: Object,
    crop: Object
  },
  emits: ['add', 'close'],
  data () {
    return {
      loading: false
    }
  },
  methods: {
    submitForm (e, crop) {
      this.loading = true
      e.preventDefault()
      createCrop(crop).then(response => {
        this.$emit('add', response.data.data)
      }).finally(() => {
        this.loading = false
        this.$emit('close')
      })
    }
  }
}
</script>

<style scoped lang="scss">
</style>