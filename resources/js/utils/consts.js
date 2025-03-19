
const defaultCrop = (plant = null) => {
  return {
    plant_id: plant ? plant.id : null,
    crop_entries: [],
    spacing: 1,
    height: 1,
    width: 1,
    qty: 1
  }
}

const defaultCropEntry = (bed = null, crop = null) => {
  return {
    action: 'Planned',
    stage: 'Planned',
    notes: null,
    images: [],
    datetimestamp: new Date().toISOString().slice(0, 16),
    crop_id: crop ? crop.id : null
  }
}

const defaultBed = () => {
  return {
    x: 0,
    y: 0,
    width: 0,
    height: 0,
    name: '',
    description: '',
    images: []
  }
}

const defaultPlant = () => {
  return {
    name: '123',
    variety: '456',
    description: '456',
    spacing: 1,
    sow_from: 1,
    sow_to: 1,
    days_to_harvest: 1,
    image: ''
  }
}

export {
  defaultCrop,
  defaultCropEntry,
  defaultBed,
  defaultPlant
}