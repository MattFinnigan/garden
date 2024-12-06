<template>
  <div class="crop-form">
    <CropEntryForm
      v-if="currentEntry"
      title="Add a Crop"
      :handleSubmit="false"
      @submit="submitForm"
      @close="done"/>
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
  data () {
    return {
      loading: false
    }
  },
  computed: {
    current () {
      return this.$store.state.crops.current
    },
    currentEntry () {
      return this.$store.state.crop_entries.current
    }
  },
  methods: {
    submitForm (e, entry) {
      this.loading = true
      e.preventDefault()
      this.$store.commit('crops/setCurrentCropEntries', [...this.current.crop_entries, entry])
      createCrop(this, { ...this.current, ...this.currentEntry }).then(response => {
        this.$store.commit('crops/setCurrentCrop', null)
        this.$store.commit('crop_entries/setCurrentCropEntry', null)
      })
    },
    done () {
      this.$store.commit('crops/setCurrentCrop', null)
      this.$store.commit('crop_entries/setCurrentCropEntry', null)
    }
  }
}
</script>

<style scoped lang="scss">
</style>