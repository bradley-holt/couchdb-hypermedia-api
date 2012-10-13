function(doc) {
    if (!doc.collection) {
        emit(doc._id, doc.resource);
    }
}
