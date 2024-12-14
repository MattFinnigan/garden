
const defaultCrop = (plant = null) => {
  return {
    plant_id: plant ? plant.id : null,
    crop_entries: []
  }
}

const defaultCropEntry = (location, bed = null, crop = null) => {
  return {
    location_id: location.id,
    bed_id: bed?.id || null,
    action: 'Planned',
    stage: 'Planned',
    qty: 1,
    notes: null,
    images: [],
    spacing_x: 1,
    spacing_y: 1,
    datetimestamp: new Date().toISOString().slice(0, 16),
    crop_id: crop ? crop.id : null
  }
}

export {
  defaultCrop,
  defaultCropEntry
}