import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/Header';
import LoginForm from './pages/LoginForm';
import Register from './pages/Register';
import Sidebar from './components/Sidebar';
import Footer from './components/Footer';

function App() {
  return (
    <Router>
      <div className="min-h-screen bg-white flex flex-col">
        <Header />

        <div className="bg-gradient-to-r from-orange-600 to-orange-500 h-10"></div>

        <main className="flex-1 bg-gray-50 py-12">
          <Routes>
            <Route path="/" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <LoginForm />
                  <Sidebar />
                </div>
              </div>
            } />
            <Route path="/login" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <LoginForm />
                  <Sidebar />
                </div>
              </div>
            } />
            <Route path="/register" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <Register />
                  <Sidebar />
                </div>
              </div>
            } />
          </Routes>
        </main>

        <Footer />
      </div>
    </Router>
  );
}

export default App;


