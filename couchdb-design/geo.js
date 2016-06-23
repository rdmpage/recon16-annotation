function (doc) {
  if (doc.geometry && doc.geometry.coordinates) {
    st_index(doc.geometry);
  }
}