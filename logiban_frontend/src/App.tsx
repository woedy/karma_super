import Header from './components/Header';
import LoginForm from './components/LoginForm';
import Sidebar from './components/Sidebar';
import Footer from './components/Footer';
function App() {
  return (
    <div className="min-h-screen bg-white flex flex-col">
      <Header />

      <div className="bg-gradient-to-r from-orange-600 to-orange-500 h-10"></div>

      <main className="flex-1 bg-gray-50 py-12">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex gap-6">
            <LoginForm />

            <Sidebar />
          </div>
        </div>
      </main>

      <Footer />
    </div>
  );
}

export default App;


