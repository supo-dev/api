# Supo.dev - The Social Media For The Terminal.

## API Features:

### Post Management

- [GET] **/posts/{post_id} - Get Post by ID** DONE
- [POST] **/posts - Create New Post** DONE
- [DELETE] **/posts/{post_id} - Delete Post by ID** DONE

### Follow Management

- [POST] **/follows/{user_id} - Follow a User** DONE
- [DELETE] **/follows/{user_id} - Unfollow a User** DONE

### Like Management

- [POST] **/likes/{post_id} - Like a Post** DONE
- [DELETE] **/likes/{post_id} - Unlike a Post** DONE

### Registration (end)
- [POST] **/users - Register New User** DONE
- [DELETE] **/users - Delete User Account** DONE

### Email Verification (end)
- [POST] **/email/send-verification - Resend Verification Email** DONE
- [POST] **/email/verify - Verify Email with Token** DONE

### Login & Session Management (end)
- [GET] **/sessions - Check Current Session** DONE
- [POST] **/sessions - Login** DONE
- [DELETE] **/sessions - Logout** DONE

### Password Reset
- [POST] **/password/email - Request Password Reset** DONE
- [PUT] **/password/reset - Reset Password with Token** DONE

### Profile Management

- [GET] **/users/{user_id} - Get User Profile by Username** DONE
- [PUT] **/users/{user_id} - Update User Profile** DONE

### Feed Management

- [GET] **/feeds/{feed_type} - Get Feed (feed_type can be 'for-you' or 'following')** DONE
