function(doc) {
    emit([doc._id, 0], doc.resource);
    if (doc.collection) {
        emit([doc.collection, 1], doc.resource);
    }
}
