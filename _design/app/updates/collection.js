function(doc, req) {
    if ("DELETE" == req.method) {
        doc._deleted = true;
        return [doc, {}];
    }
    switch (req.headers["Content-Type"]) {
        case "application/hal+json":
            if (doc) {
                updatedDoc = doc;
            } else {
                var updatedDoc = {};                
            }
            updatedDoc.resource = JSON.parse(req.body);
            if (!updatedDoc._id) {
                if (req.id) {
                    updatedDoc._id = req.id;
                } else if (req.uuid) {
                    updatedDoc._id = req.uuid;
                }
            }
            updatedDoc.is_collection = true;
            updatedDoc.resource._links = {
                "self": { "href": "/" + updatedDoc._id + "/" },
                "up": { "href": "/" }
            };
            return [
                updatedDoc,
                {
                    "headers": {
                        "Location": updatedDoc.resource._links.self.href,
                        "Content-Type": "application/hal+json",
                        "Vary": "Accept"
                    },
                    "body": toJSON(updatedDoc.resource) + "\n"
                }
            ];
        default:
            return [
                null,
                {
                    "code": 415,
                    "headers": {
                        "Content-Type": "text/plain;charset=utf-8",
                        "Vary": "Accept"
                    },
                    "body": toJSON({"error":"unsupported_media_type","reason":"The media type sent is not supported."}) + "\n"
                }
            ];
    }
}
