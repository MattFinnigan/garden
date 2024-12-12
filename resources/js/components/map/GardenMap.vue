<template>
  <div v-if="!loading" class="garden-map">
    <div v-if="location" class="controls-row">
      <Select v-model.number="location.id" :options="maps.map(l => { return { label: l.name, value: l.id } })" label="Location"/>
      <div class="date-select">
        <Button class="icon secondary" @click="removeDays(7)"><Icon name="rewind" size="16px" maskSize="15px"></Icon></Button>
        <Button class="icon secondary" @click="removeDays(1)"><Icon name="play reverse"></Icon></Button>
        <Input type="date" v-model="date" @change="fetchMaps"/>
        <Button class="icon secondary" @click="addDays(1)"><Icon name="play"></Icon></Button>
        <Button class="icon secondary" @click="addDays(7)"><Icon name="rewind reverse" size="16px" maskSize="15px"></Icon></Button>
      </div>
      <div class="buttons-contain">
        <Button class="primary outline icon">Grid</Button>
        <Button class="primary icon"><Icon name="plus"></Icon></Button>
      </div>
    </div>
    <div class="grid">
      <BedMap v-for="bed in location.beds" :key="'bed' + bed.id" :bed="bed"/>
    </div>
  </div>
</template>

<script>
import { fetchMaps } from '../../utils/api'
import { arrangePlantsInBedWithOverlapCheck } from '../../utils/helpers'

import BedMap from './BedMap.vue'

export default {
  name: 'GardenMap',
  components: {
    BedMap
  },
  data () {
    return {
      loading: true,
      date: new Date().toISOString().split('T')[0],
      location: null
    }
  },
  mounted () {
    this.fetchMaps()
  },
  computed: {
    maps () {
      return this.$store.state.maps.list
    }
  },
  methods: {
    fetchMaps () {
      this.loading = true
      fetchMaps(this, this.date).then(response => {
        this.loading = false
        this.location = this.maps[0]
        this.location.beds.forEach(bed => {
          bed.crop_entries.forEach(ce => {
            // console.log(JSON.stringify(arrangePlantsInBedWithOverlapCheck(ce, bed)))
          })
        })
      })
    },
    addDays (days) {
      const date = new Date(this.date)
      date.setDate(date.getDate() + days + 1)
      this.date = date.toISOString().split('T')[0]
      this.fetchMaps()
    },
    removeDays (days) {
      const date = new Date(this.date)
      date.setDate(date.getDate() - days)
      this.date = date.toISOString().split('T')[0]
      this.fetchMaps()
    },
  }
}
</script>

<style scoped lang="scss">
.controls-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 1.375em;
  > * {
    flex: 0.3;
  }
  .date-select {
    display: flex;
    align-items: center;
    gap: 10px;
  }
  .buttons-contain {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    gap: 10px;
  }
}
.grid {
  position: relative;
  background: $primary2;
  height: 500px;
  width: 100%;
}
</style>