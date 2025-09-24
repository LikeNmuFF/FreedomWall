# Phoenix Freedom Wall 🚀🔥

A student-friendly message wall with admin approval system.

A flowchart illustrating the user and system interactions for the Phoenix Freedom Wall

This system has three primary components: the user submission form, the admin approval panel, and the public live display.

### **User Interaction Flow**

This path shows how a student or any general user submits a message.

```
            ┌──────────────────────┐
            │   Student / User     │
            └──────────────────────┘
                    │
                    ▼
┌────────────────────────────────────┐
│ 1. Submission Portal (index.php)   │  ← User fills form: Name, Year, Course, Message.
└────────────────────────────────────┘      Can also select "Anonymous".
                    │
                    ▼
┌────────────────────────────────────┐
│ 2. System: Validation & Spam Check │  ← Checks character limits and 60s cooldown.
└────────────────────────────────────┘
                    │
         ┌──────────┴──────────┐
         ▼                     ▼
┌──────────────────┐  ┌─────────────────────────────────┐
│ On Error         │  │ On Success                      │
│                  │  │                                 │
│ Redisplay form   │  │ Store message in Database       │
│ with error message.│  │ with status set to "Pending".   │
└──────────────────┘  └─────────────────────────────────┘
                                     │
                                     ▼
                      ┌─────────────────────────────────┐
                      │ User sees success message:      │
                      │ "Pending admin approval."       │
                      └─────────────────────────────────┘
```

-----

### **Admin Interaction Flow**

This path shows how an administrator moderates the submitted messages.

```
            ┌──────────────────────┐
            │         Admin        │
            └──────────────────────┘
                    │
                    ▼
┌────────────────────────────────────┐
│ 1. Admin Login (auth.php)          │  ← Secure access to the dashboard.
└────────────────────────────────────┘
                    │
                    ▼
┌────────────────────────────────────┐
│ 2. Admin Dashboard (admin.index.php)│ ← System fetches all "Pending" messages.
└────────────────────────────────────┘
                    │
                    ▼
┌────────────────────────────────────┐
│ 3. Admin Reviews Pending Messages  │
└────────────────────────────────────┘
                    │
         ┌──────────┴──────────┐
         ▼                     ▼
┌──────────────────┐  ┌─────────────────────────────────┐
│ Action: Approve  │  │ Action: Delete                  │
│                  │  │                                 │
│ System updates   │  │ System permanently removes      │
│ message status   │  │ the message from the Database.  │
│ to "Approved".   │  │                                 │
└──────────────────┘  └─────────────────────────────────┘
         │
         └──────────┐
                    ▼
```

-----

### **System & Database Flow**

This shows how the database and the automated live display screen interact.

```
                    ▼
┌────────────────────────────────────┐
│    MySQL Database ("messages" table) │  ← Central storage for all message data.
└────────────────────────────────────┘
                    ▲
         ┌──────────┴──────────┐
         │                     │
         ▼                     ▼
┌──────────────────┐  ┌─────────────────────────────────┐
│ 4. Live Display  │  │ 5. System: Auto-Cleanup         │
│    (home.php)    │  │                                 │
│                  │  │ System automatically deletes    │
│ Fetches & shows  │  │ "Approved" messages that are    │
│ "Approved"       │  │ older than 3 hours from the     │
│ messages.        │  │ Database to keep content fresh. │
└──────────────────┘  └─────────────────────────────────┘
         │
         ▼
┌────────────────────────────────────┐
│ Display screen rotates through     │  ← TV screen visible to the public.
│ messages and auto-refreshes for    │    Refreshes every 5s for new content.
│ new content.                       │    Rotates pages every 10s.
└────────────────────────────────────┘
```

## 📸 Screenshots

### 🔑 Admin Approval Center
![Admin Approval Center](UI/approval_center_with_message.png)

### ✅ No Pending Messages
![No Pending Messages](UI/approval_center.png)

### 📝 Freedom Wall with Messages
![Freedom Wall Messages](UI/FreedomWall_wth_messages.png)

### ⏳ Awaiting Messages
![Freedom Wall Empty](UI/FreedomWall_wthout_message.png)

### 📱 Mobile View
![Mobile View](UI/phone_pov.jpg)
