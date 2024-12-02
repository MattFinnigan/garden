<template>
  <div class="bed-input">
    <div class="inputs-wrapper">
      <Input v-model="bed.name" label="Name" maxlength="255" required/>
      <Input v-model="bed.description" label="Description" maxlength="255"/>
      <Input :modelValue="bed.image" type="file" label="Image" @change="e => bed.image = e.target.value"/>
    </div>
    <div class="buttons-wrapper">
      <Button classes="sm" @click="() => { if (bed.name.length) { $emit('done', bed) } }">{{ bed.id || bed.index ? 'Update' : 'Add' }}</Button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'BedInput',
  props: {
    exisiting: Object
  },
  data () {
    return {
      bed: {
        name: '',
        description: '',
        image: ''
      }
    }
  },
  mounted() {
    if (this.exisiting) {
      this.bed = this.exisiting
    }
  }
}
</script>

<style scoped lang="scss">
.bed-input {
  :deep(input) {
    min-width: 350px;
    height: 35px;
    padding-left: 0.5em;
    background: $grey-700;
    color: $white;
    outline: none;
    border: 1px solid $grey-500;
    border-radius: 0.25em;
  }
}
</style>