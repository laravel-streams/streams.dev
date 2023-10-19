from locust import HttpUser, task

class NewVisitor(HttpUser):

    def on_start(self):
        self.client.headers = {'User-Agent': 'locust'}

    @task(2)
    def homepage(self):
        self.client.get("/")

    @task(2)
    def docs(self):
        self.client.get("/docs")
    
    @task(2)
    def installation(self):
        self.client.get("/docs/installation")
    
    @task(1)
    def explore(self):
        self.client.get("/explore/idea")
