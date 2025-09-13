# Mini Aplikasi Project Tracker

## Fitur:

1. **CRUD Project**
   - User dapat membuat, membaca, memperbarui, dan menghapus project.
   - Field yang tersedia:
     - Nama
     - Status (Draft, In Progress, Done)
     - Description Progress (%)

2. **CRUD Task**
   - User dapat membuat, membaca, memperbarui, dan menghapus Task.
   - Field yang tersedia:
     - Nama
     - Status (Draft, In Progress, Done)
     - Belongs to (the project)
     - Bobot (category)

3. **Penghitungan Progress Project**
   - Completion progress pada project dipengaruhi dari yang selain task yang selesai (status 'Done') dan bobotnya.
   - Jika terdapat 2 task:
     - Task 1 memiliki bobot 2, dan task 1 selesei, maka progress project adalah 66,6% (rumus (bobot bobot task done / total bobot task) x 100).
     - Task 2 memiliki bobot 1.

4. **Status Project**
   - Status project tidak dapat diubah secara langsung oleh user, melainkan dipengaruhi oleh status task.
   - Jika semua task masih dalam status 'Draft', maka status project adalah 'Draft'.
   - Jika ada satu task yang berstatus 'In Progress', maka status project tersebut menjadi 'In Progress'.
   - Jika semua task berstatus 'Done', maka status project tersebut menjadi 'Done'.

5. **Single Page Application**
   - Aplikasi dibuat dalam bentuk single page dengan menggunakan AJAX untuk create, read, update, dan delete data .

6. **Modal atau Sidebar Container**
   - Proses create, edit, dan update di handle dalam modal atau sidebar container untuk pengalaman pengguna yang lebih baik .

7. **Real-time Data Manipulation**
   - Data di-update secara real-time dengan manipulasi DOM langsung setelah proses create, read, update, dan delete selesei .