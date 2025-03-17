<template>
  <div>
    <p v-if="errors" class="error">{{ errors }}</p>
    <Form :canDelete="canDelete" deleteText="Remove" :deleteWarning="'Remove bed from ' + localeDate(date) + '?' " @remove="$emit('remove', current)" @submit="submitForm">
      <template #inputs>
        <Input v-model="name" label="Name" maxlength="255" :flex="true" placeholder="Herbs Bed" required/>
        <Input v-model="description" type="textarea" label="Description" placeholder="Nice sunny location. Snails seem to hate it here!" :flex="true" maxlength="255"/>
        <Images class="images" :modelValue="images" label="Images" multiple @addImage="addImage" @removeImage="removeImage"/>
      </template>
      <template #buttons>
        <slot name="buttons"></slot>
      </template>
    </Form>
  </div>
</template>

<script>
import { clone, localeDate } from '../../utils/helpers'
import { createBed } from '../../utils/api'

export default {
  name: 'BedsForm',
  emits: ['done', 'remove'],
  data () {
    return {
      localeDate,
      errors: false,
      loading: false
    }
  },
  computed: {
    current () {
      return this.$store.state.beds.current
    },
    date () {
      return new Date().toISOString().split('T')[0]
    },
    name: {
      get () {
        return this.current.name
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedName', value)
      }
    },
    w: {
      get () {
        return this.current.w
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedWidth', value)
      }
    },
    l: {
      get () {
        return this.current.l
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedLength', value)
      }
    },
    description: {
      get () {
        return this.current.description
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedDescription', value)
      }
    },
    images: {
      get () {
        return this.current.images
      },
      set (value) {
        this.$store.commit('beds/setCurrentBedImages', value)
      }
    },
    canDelete () {
      return this.current.id && this.current.crop_entries.length === 0 || true
    }
  },
  methods: {
    addImage (image) {
      this.$store.commit('beds/addImageToCurrentBed', { bed_id: this.current.id || 0, name: image })
    },
    removeImage (index) {
      this.$store.commit('beds/removeImageFromCurrentBed', index)
    },
    submitForm (e) {
      e.preventDefault()
      if (this.loading) {
        return
      }
      this.errors = false
      if (!this.name.length) {
        this.errors = 'Name is required'
      }
      if (!this.errors) {
        this.loading = true
        let beds = clone(this.beds)
        if (this.current.id) {
          beds = beds.map(b => {
            if (b.id === this.current.id) {
              return this.current
            }
            return b
          })
        } else {
          createBed(this, { ...this.current }).then(response => {
            if (response.data.status === 'success') {
              this.$store.commit('beds/setCurrentBed', response.data.bed)
              this.$emit('done', response.data.bed)
            } else {
              this.errors = response.data.message
            }
            this.loading = false
          })
        }
      }
    }
  }
}
</script>

<style scoped lang="scss">
.images {
  :deep(.images-preview) {
    max-height: 300px;
    overflow-y: scroll;
  }
}
</style>