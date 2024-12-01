<template>
  <div class="select-container">
    <label>{{ label }}</label>
    <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" v-bind="$attrs">
      <option v-for="option in options" :value="option.value">{{ option.label }}</option>
    </select>
  </div>
</template>

<script>
export default {
  name: 'Select',
  props: {
    label: {
      type: String,
      required: true
    },
    options: {
      type: Array,
      required: true
    },
    modelValue: {
      required: true
    }
  },
  emits: ['update:modelValue']
}
</script>

<style scoped lang="scss">
.select-container {
  display: flex;
  flex-direction: column;
  margin-bottom: 0.75rem;
  label {
    margin-bottom: 0.25rem;
  }
}
</style>