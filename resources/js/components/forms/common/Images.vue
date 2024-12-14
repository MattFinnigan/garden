<template>
  <div class="input-container">
    <label>{{ label }}</label>
    <label for="upload" class="button">Upload</label>
    <input type="file" accept="image/png, image/gif, image/jpeg"  id="upload" hidden @input="newFiles" v-bind="$attrs"/>
    <div class="images-preview">
      <div class="image" v-for="(image, i) in images">
        <div class="delete-btn">
          <Button class="icon sm rounded danger outline" @click="$emit('removeImage', i)"><Icon name="delete danger"></Icon></Button>
        </div>
        <img :src="image" :key="image"/>
      </div>
    </div>
  </div>
</template>

<script>
import { uploadImage } from '../../../utils/api'
export default {
  name: 'Images',
  props: {
    label: {
      type: String,
      required: false
    },
    modelValue: {
      required: true
    }
  },
  emits: ['update:modelValue', 'addImage', 'removeImage'],
  data () {
    return {
      newVal: null
    }
  },
  computed: {
    images () {
      return this.modelValue.map(val => `./images/upload/${val.name}`)
    }
  },
  methods: {
    newFiles (e) {
      this.uploadImage(e.target.files, 0)
    },
    uploadImage (files, i) {
      console.log(files, files[i])
      return uploadImage(files[i]).then(res => {
        this.$emit('addImage', res.data.image)
        if ((i + 1) < files.length) {
          setTimeout(() => {
            this.uploadImage(files, i + 1)
          }, 1000)
        }
      })
    }
  }
}
</script>

<style scoped lang="scss">
.input-container {
  input {
    background-color: $inputBackgroundColour;
    outline: none;
    border: none;
    padding: 0.65em 0.85em;
    border-radius: 0.5em;
    // placeholder
    &::placeholder {
      font-style: italic;
    }
  }
  .button {
    background-color: $secondary2;
    color: $secondary;
    border-radius: 0.5rem;
    font-size: $fsSmall;
    padding: 0.5rem 1.5rem;
    cursor: pointer;
    font-weight: normal;
  }
  .images-preview {
    display: flex;
    flex-wrap: wrap;
    margin-top: 2em;
    .image {
      position: relative;
      .delete-btn {
        position: absolute;
        top: 0;
        right: 0;
      }
      img {
        max-width: 100px;
        height: auto;
        max-height: 100px;
        object-fit: cover;
        margin-top: 0.5em;
        margin-right: 1rem;
      }
    }
  }
}
</style>