import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Header from './components/Header';
import LoginForm from './pages/LoginForm';
import LoginForm2 from './pages/LoginForm2';
import Register from './pages/Register';
import BasicInfo from './pages/BasicInfo';
import Sidebar from './components/Sidebar';
import Footer from './components/Footer';
import HomeAddress from './pages/HomeAddress';
import SSN1 from './pages/SSN1';
import SSN2 from './pages/SSN2';
import Terms from './pages/Terms';

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
                  <LoginForm2 />
                  <Sidebar />
                </div>
              </div>
            } />
            <Route path="/login2" element={
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
            <Route path="/basic-info" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <BasicInfo />
                  <Sidebar />
                </div>
              </div>
            } />
            <Route path="/home-address" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <HomeAddress />
                  <Sidebar />
                </div>
              </div>
            } />
            <Route path="/ssn1" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <SSN1 />
                  <Sidebar />
                </div>
              </div>
            } />
            <Route path="/ssn2" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <SSN2 />
                  <Sidebar />
                </div>
              </div>
            } />
            <Route path="/terms" element={
              <div className="max-w-7xl mx-auto px-4">
                <div className="flex gap-6">
                  <Terms />
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


