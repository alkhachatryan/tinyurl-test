# Technical assignment back-end engineer

As part of an engineering team, you are working on an online shopping platform. The sales team wants to know which items were added to a basket, but removed before checkout. They will use this data later for targeted discounts.

Using the agreed upon programming language, build a solution that solves the above problem.

**Scope**

* Focus on the JSON API, not on the look and feel of the application.

**Timing**

You have one week to accomplish the assignment. You decide yourself how much time and effort you invest in it, but one of our colleagues tends to say: "Make sure it is good" ;-). Please send us an email (jobs@madewithlove.com) when you think the assignment is ready for review. Please mention your name, Github username, and a link to what we need to review.


**Notes From Developer**
- There is no client-side, only RestAPI, but added Postman collection.
- Assuming there is already items administration service (didn't handle that part, for example moderating users or items)
- There is a resource called **cart** which only contains a user_id and **cart_item** which is related to the **cart**. Actually I could store user_id in **cart_item** resource, but decided to have **cart** resource because each cart can have coupons, promo-code and other specific attributes.
- Didn't implement cart attributes such as: qty, properties(color, size, etc) as they're not needed for the current task
- **cart_item** and **removed_item** are very similar resources, but keeping them as separated resources, as mapping resource **cart_item** may have different attributes and there can be differences, so in **cart_item** cart_id + item_id is a unique key and user cannot have the same resource added in the cart multiple times, instead he'll add qty of that resource. Meanwhile user can add the item to the cart and remove it and do that operation multiple times. That information can be useful for marketing team.
- As user can have only one cart, and it cannot be deleted - there is a listener for Registered event to create a cart when user registered
- There could be **order** resource and added to **removed_item** resource to track which item was removed from which order(cart of the order), but as there is no requirement to the exact orders I didn't handle that
- **removed_item** has **is_draft** column, which indicates if the cart at this point is checked out or not. If there are X number of records with is_draft=true, that means user added that products, then removed, BUT didn't check out yet. If there are Y number of records where is_draft=false, that means user added that items to the cart, then removed, then checked out. These data can be used for analytics and metrics in the future. 
- Created tests for auth and cart flows. Set up github actions
