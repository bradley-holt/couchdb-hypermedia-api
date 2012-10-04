function(doc) {
    if (doc.is_collection) {
        emit(doc._id, doc.resource.title);
    }
}
