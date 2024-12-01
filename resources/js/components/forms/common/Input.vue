<template>
  <div class="input-container">
    <label>{{ label }}</label>
    <input :type="type" :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" :placeholder="label" v-bind="$attrs"/>
  </div>
</template>

<script>
export default {
  name: 'Input',
  props: {
    label: {
      type: String,
      required: true
    },
    type: {
      type: String,
      default: 'text'
    },
    modelValue: {
      required: true
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped lang="scss">
.input-container {
  display: flex;
  flex-direction: column;
  margin-bottom: 0.75rem;
  label {
    margin-bottom: 0.25rem;
  }
  input[type=file] {
    padding: 1em;
    padding-bottom: 0;
  }
}
</style>