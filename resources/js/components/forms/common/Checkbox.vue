<template>
  <div :class="['input-container']">
    <label>
      {{ label }}
      <input type="checkbox" :value="modelValue" :checked="modelValue" @input="$emit('update:modelValue', $event.target.value)" v-bind="$attrs"/>
    </label>
    <slot name="suffix"></slot>
  </div>
</template>

<script>
export default {
  name: 'Checkbox',
  emits: ['update:modelValue'],
  props: {
    label: {
      type: String,
      required: true
    },
    modelValue: {
      required: true
    }
  },
  methods: {
    toggle(event) {
      this.$emit('change', event.target.checked)
    }
  }
}
</script>

<style scoped lang="scss">
.input-container {
  label {
    grid-template-columns: 1em auto;
    gap: 0.5em;
  }
  input {
    background-color: $inputBackgroundColour;
    outline: none;
    border: none;
    padding: 0.65em 0.85em;
    border-radius: 0.5em;
    -webkit-appearance: none;
    appearance: none;
    background-color: #fff;
    margin: 0;
    color: currentColor;
    width: 1.15em;
    height: 1.15em;
    border: 0.15em solid currentColor;
    border-radius: 0.15em;
    transform: translateY(-0.075em);
  }
}
</style>