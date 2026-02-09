# API Documentation

## Authentication
All endpoints require a Bearer token in the Authorization header: `Authorization: Bearer <token>`

## Videos

### List Videos
`GET /api/videos`

Retrieves a paginated list of videos for the authenticated user.

**Query Parameters**
- `page` (integer, optional): Page number for pagination. Defaults to 1.
- `platform` (string, optional): Filter videos by platform. If provided, returns only videos that have at least one scheduled post for the specified platform.
  - Example: `?platform=youtube`
  - Example: `?platform=tiktok`

**Response**
Returns a JSON object containing a `data` array of videos and pagination links.

### Upload Video
`POST /api/videos`

Uploads a new video file.

**Body Parameters**
- `video` (file, required): The video file to upload.

### Get Video
`GET /api/videos/{id}`

Retrieves details of a specific video.

### Delete Video
`DELETE /api/videos/{id}`

Deletes a video. Note: Cannot delete a video if it has associated scheduled posts.

## Schedule

### List Scheduled Posts
`GET /api/schedule`
...
