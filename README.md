# Library Reservation System

# Synopsis 

Library provides the university’s students with resources for study or research, such as textbooks. We currently use a paper-based system to keep track of students’ reservation and borrowing activities. We are in need of a new online reservation system for our books as not only have some students stolen our books and not returned them, but we’ve also been accidentally allowing multiple students to reserve the same book, which has led to a lot of conflict within the reservation system. We are in need of a new online web application that can help us keep a track of who has reserved and borrowed what, and enable students to reserve books from the Library. This online system must feature a graphical user interface, while delivering full reservation functionality with the ability to inform students when the return dates on their borrowed books are due by sending them reminders.

# Deliverables and schedule

A website with a front end coded in HTML, CSS and JavaScript, and a backend consisting purely of PHP with 2 SQL databases for books and student accounts respectively.
The website shall track the current status of books, if they’re available, reserved or unavailable and if a book has been returned within its due date (2 weeks).
Staff will be notified when a book has been reserved through the system.

| No. |                                      Item                                                       | Release Schedule  |
| --- |:-----------------------------------------------------------------------------------------------:| :----------------:|
| F2  | Student login page for existing students                                                        |      Sprint 1     |
| F2  | Search for books satisfying the student’s criteria (e.g., title, author, category, etc.)        |      Sprint 1     |
| F3  | Display details and availability of a selected book from the output of F2                       |      Sprint 1     |
| F4  | Add a book reservation record of a selected book from the output of F2 or F3, for up to 3 weeks |      Sprint 1     |
| F5  | View a student’s current book reservation record                                                |      Sprint 1     |
| F6  | Cancel a book reservation before collection                                                     |      Sprint 1     |
| F7  | Send the user a notification once their reserved book is available to be picked up              |      Sprint 2     |
| F8  | Renew the reservation on a loaned book for a maximum of 3 weeks, up to 2 times                  |      Sprint 2     |
| F9  | Alert the user once their book is due to be returned                                            |      Sprint 2     |
| F10 | Alert the user if their book is overdue and that late charges will be incurred                  |      Sprint 2     |
