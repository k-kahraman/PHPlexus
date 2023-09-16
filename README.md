# PHPlexus: Minimalist, Powerful, and Simple PHP Framework
Welcome to PHPlexus, where simplicity meets robust development. At its core, PHPlexus is designed to provide developers with a streamlined and intuitive interface to build web applications without the bloat of larger frameworks. Yet, it doesn't compromise on power or flexibility. Here's what sets PHPlexus apart:

## 1. 🧰 Custom Dependency Injection System:
PHPlexus boasts a homegrown, lightweight, and easy-to-understand Dependency Injection (DI) system. With our DI, you can manage your application's services and dependencies efficiently without overhead. The DI container is minimal, meaning it's quick to grasp and simple to use, yet provides a powerful mechanism for service provision and autowiring.

## 2. 🌐 Enhanced HTTP Handling:
While PHP provides basic HTTP request and response handling, PHPlexus takes this a step further. With custom Request and Response objects, developers have fine-grained control over incoming HTTP requests and outgoing responses, making building APIs and web services a breeze.

## 3. 🏗️ Entity-Repository-Service-Controller (ERSC) Architecture:
Dive into an intuitive way of organizing your application logic with the ERSC pattern:

- **Entities**: These are simple, powerful, and immutable objects representing your domain logic. With native support for both Models (mutable) and Records (immutable), developers can choose the best paradigm for their needs.

- **Repositories**: Interact with your data store seamlessly. Out of the box, we support MySQL and Redis, but PHPlexus is designed to be extensible. Developers can plug in their preferred data stores by simply implementing our foundational repository interface.

- **Services**: Intended to house your business logic, services in PHPlexus act as intermediaries between controllers and repositories, ensuring a clean separation of concerns.

- **Controllers**: Handle incoming HTTP requests, manipulate data through services, and send back HTTP responses. With PHPlexus, controllers are slim, efficient, and easy to understand.

## 4. ⚡ Performance:
PHPlexus is designed with performance in mind. By cutting down on unnecessary overhead and focusing only on essential components, we ensure your applications run as swiftly as possible.

## 5. 📚 Extensibility:
While PHPlexus provides a solid base for web application development, it's built to be extensible. Whether you're looking to integrate a third-party package, create custom handlers, or expand on existing functionality, PHPlexus is malleable to your needs.

---

#### Join the Revolution: Dive into the world of PHPlexus and experience PHP web development like never before. Whether you're a seasoned developer looking for a lightweight alternative or a newbie starting your journey, PHPlexus is tailored for you.
